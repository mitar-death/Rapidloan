output "app_url" {
  description = "The URL of the Laravel application"
  value       = "http://${var.domain}"
}

output "phpmyadmin_url" {
  description = "The URL of phpMyAdmin"
  value       = "http://pma.${var.domain}"
}

output "vps_ip" {
  description = "The IP address of the VPS"
  value       = var.vps_ip
}
