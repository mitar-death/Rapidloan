variable "vps_ip" {
  description = "The IP address of the VPS"
  type        = string
}

variable "ssh_user" {
  description = "SSH username for the VPS"
  type        = string
  default     = "root"
}

variable "ssh_pass" {
  description = "SSH password for the VPS"
  type        = string
  sensitive   = true
}

variable "domain" {
  description = "The domain name for the application"
  type        = string
}

variable "git_repo" {
  description = "The Git repository URL for the Laravel app"
  type        = string
}

variable "db_name" {
  description = "The name of the database to create"
  type        = string
  default     = "laravel_db"
}

variable "db_user" {
  description = "The database user to create"
  type        = string
  default     = "laravel_user"
}

variable "db_password" {
  description = "The password for the database user"
  type        = string
  sensitive   = true
}

variable "app_key" {
  description = "Laravel APP_KEY (generate with php artisan key:generate if empty)"
  type        = string
  default     = ""
}
