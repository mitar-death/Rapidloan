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

resource "null_resource" "laravel_setup" {
  depends_on = [null_resource.upload_db_dump]

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
    })
    destination = "/tmp/setup.sh"
  }

  provisioner "file" {
    content = templatefile("${path.module}/configs/nginx-laravel.conf.tftpl", {
      domain = var.domain
    })
    destination = "/tmp/nginx-laravel.conf"
  }

  provisioner "file" {
    content = templatefile("${path.module}/configs/nginx-pma.conf.tftpl", {
      domain = var.domain
    })
    destination = "/tmp/nginx-pma.conf"
  }

  provisioner "remote-exec" {
    inline = [
      "chmod +x /tmp/setup.sh",
      "sudo /tmp/setup.sh",
      "sudo mv /tmp/nginx-laravel.conf /etc/nginx/sites-available/laravel",
      "sudo mv /tmp/nginx-pma.conf /etc/nginx/sites-available/phpmyadmin",
      "sudo ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/",
      "sudo ln -sf /etc/nginx/sites-available/phpmyadmin /etc/nginx/sites-enabled/",
      "sudo rm -f /etc/nginx/sites-enabled/default",
      "sudo systemctl reload nginx"
    ]
  }

  triggers = {
    setup_script       = filesha1("${path.module}/scripts/setup.sh.tftpl")
    nginx_laravel_conf = filesha1("${path.module}/configs/nginx-laravel.conf.tftpl")
    nginx_pma_conf     = filesha1("${path.module}/configs/nginx-pma.conf.tftpl")
  }
}
