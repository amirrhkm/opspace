variable "bucket_name" {
  type        = string
  description = "Name of the S3 bucket"
}

variable "region" {
  type        = string
  description = "AWS region for the bucket"
  default     = "us-east-1"
}

variable "tags" {
  type        = map(string)
  description = "Tags to apply to the bucket"
  default     = {}
}

variable "versioning_enabled" {
  type        = bool
  description = "Whether to enable versioning for the bucket"
  default     = false
} 