variable "IMAGES_PREFIX" { default = "" }
variable "IMAGES_TAG" { default = "latest" }

target "prod" {
  context = ".."
  dockerfile = "docker/Dockerfile"
  tags = ["${IMAGES_PREFIX}discord-eraser-bot:${IMAGES_TAG}"]
  target = "app_prod"
}

group "default" {
  targets = ["prod"]
}
