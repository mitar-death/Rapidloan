# Upload SQL dump to VPS (only when db_dump_path is set)
resource "null_resource" "upload_db_dump" {
  count = var.db_dump_path != "" ? 1 : 0

  connection {
    type     = "ssh"
    user     = var.ssh_user
    password = var.ssh_pass
    host     = var.vps_ip
  }

  provisioner "file" {
    source      = var.db_dump_path
    destination = "/tmp/db_import.sql"
  }

  triggers = {
    dump_file = var.db_dump_path
  }
}

locals {
  has_ssl = (
    var.ssl_cert_path != "" && var.ssl_key_path != "" &&
    try(fileexists(var.ssl_cert_path), false) && try(fileexists(var.ssl_key_path), false)
  )
}

resource "null_resource" "upload_ssl_certs" {
  count = local.has_ssl ? 1 : 0

  connection {
    type     = "ssh"
    user     = var.ssh_user
    password = var.ssh_pass
    host     = var.vps_ip
  }

  provisioner "file" {
    source      = var.ssl_cert_path
    destination = "/tmp/certificate.pem"
  }

  provisioner "file" {
    source      = var.ssl_key_path
    destination = "/tmp/private_key.pem"
  }
}

resource "null_resource" "laravel_setup" {
  depends_on = [null_resource.upload_db_dump, null_resource.upload_ssl_certs]

  connection {
    type     = "ssh"
    user     = var.ssh_user
    password = var.ssh_pass
    host     = var.vps_ip
  }

  provisioner "file" {
    content = templatefile("${path.module}/scripts/setup.sh.tftpl", {
      db_name          = var.db_name
      db_user          = var.db_user
      db_password      = var.db_password
      domain           = var.domain
      git_repo         = var.git_repo
      app_key          = var.app_key
      db_dump_uploaded = var.db_dump_path != "" ? "true" : "false"
      has_ssl          = local.has_ssl
    })
    destination = "/tmp/setup.sh"
  }

  provisioner "remote-exec" {
    inline = [
      "chmod +x /tmp/setup.sh",
      "sudo /tmp/setup.sh"
    ]
  }

  triggers = {
    setup_script = filesha1("${path.module}/scripts/setup.sh.tftpl")
  }
}

resource "null_resource" "nginx_config" {
  depends_on = [null_resource.laravel_setup, null_resource.upload_ssl_certs]

  connection {
    type     = "ssh"
    user     = var.ssh_user
    password = var.ssh_pass
    host     = var.vps_ip
  }

  provisioner "file" {
    content = templatefile("${path.module}/configs/nginx-laravel.conf.tftpl", {
      domain  = var.domain
      has_ssl = local.has_ssl
    })
    destination = "/tmp/nginx-laravel.conf"
  }

  provisioner "file" {
    content = templatefile("${path.module}/configs/nginx-pma.conf.tftpl", {
      domain  = var.domain
      has_ssl = local.has_ssl
    })
    destination = "/tmp/nginx-pma.conf"
  }

  provisioner "file" {
    content = templatefile("${path.module}/configs/nginx-default.conf.tftpl", {
      has_ssl = local.has_ssl
    })
    destination = "/tmp/nginx-default.conf"
  }

  provisioner "remote-exec" {
    inline = [
      "sudo mkdir -p /etc/nginx/ssl",
      local.has_ssl ? "sudo mv /tmp/certificate.pem /etc/nginx/ssl/certificate.pem" : "echo 'No SSL cert'",
      local.has_ssl ? "sudo mv /tmp/private_key.pem /etc/nginx/ssl/private_key.pem" : "echo 'No SSL key'",
      "sudo chmod 600 /etc/nginx/ssl/private_key.pem 2>/dev/null || true",
      "sudo mv /tmp/nginx-laravel.conf /etc/nginx/sites-available/laravel",
      "sudo mv /tmp/nginx-pma.conf /etc/nginx/sites-available/phpmyadmin",
      "sudo mv /tmp/nginx-default.conf /etc/nginx/sites-available/default-block",
      "sudo ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/",
      "sudo ln -sf /etc/nginx/sites-available/phpmyadmin /etc/nginx/sites-enabled/",
      "sudo ln -sf /etc/nginx/sites-available/default-block /etc/nginx/sites-enabled/",
      "sudo rm -f /etc/nginx/sites-enabled/default",
      "sudo systemctl reload nginx"
    ]
  }

  triggers = {
    nginx_laravel_conf     = filesha1("${path.module}/configs/nginx-laravel.conf.tftpl")
    nginx_pma_conf         = filesha1("${path.module}/configs/nginx-pma.conf.tftpl")
    nginx_default_conf     = filesha1("${path.module}/configs/nginx-default.conf.tftpl")
    ssl_cert               = local.has_ssl ? filesha1(var.ssl_cert_path) : "no-ssl"
    ssl_key                = local.has_ssl ? filesha1(var.ssl_key_path) : "no-ssl"
    nginx_reconfig_trigger = var.nginx_reconfig_trigger
    # Always re-run nginx config on every apply
    always_run = timestamp()
  }
}
