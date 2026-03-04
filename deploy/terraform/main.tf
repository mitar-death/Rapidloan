resource "null_resource" "laravel_setup" {
  connection {
    type     = "ssh"
    user     = var.ssh_user
    password = var.ssh_pass
    host     = var.vps_ip
  }

  provisioner "file" {
    content = templatefile("${path.module}/scripts/setup.sh.tftpl", {
      db_name     = var.db_name
      db_user     = var.db_user
      db_password = var.db_password
      domain      = var.domain
      git_repo    = var.git_repo
      app_key     = var.app_key
    })
    destination = "/tmp/setup.sh"
  }

  provisioner "file" {
    content = templatefile("${path.module}/configs/nginx-laravel.conf.tftpl", {
      domain = var.domain
    })
    destination = "/etc/nginx/sites-available/laravel"
  }

  provisioner "file" {
    content = templatefile("${path.module}/configs/nginx-pma.conf.tftpl", {
      domain = var.domain
    })
    destination = "/etc/nginx/sites-available/phpmyadmin"
  }

  provisioner "remote-exec" {
    inline = [
      "chmod +x /tmp/setup.sh",
      "sudo /tmp/setup.sh",
      "sudo ln -sf /etc/nginx/sites-available/laravel /etc/nginx/sites-enabled/",
      "sudo ln -sf /etc/nginx/sites-available/phpmyadmin /etc/nginx/sites-enabled/",
      "sudo rm -f /etc/nginx/sites-enabled/default",
      "sudo systemctl reload nginx"
    ]
  }

  triggers = {
    setup_script      = filesha1("${path.module}/scripts/setup.sh.tftpl")
    nginx_laravel_conf = filesha1("${path.module}/configs/nginx-laravel.conf.tftpl")
    nginx_pma_conf    = filesha1("${path.module}/configs/nginx-pma.conf.tftpl")
  }
}
