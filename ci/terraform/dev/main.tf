terraform {
  required_providers {
    aws = {
      source  = "hashicorp/aws"
      version = "~> 4.0"
    }
    vault = {
      source  = "hashicorp/vault"
      version = "3.8.2"
    }
    cloudflare = {
      source  = "cloudflare/cloudflare"
      version = "~> 3.0"
    }
  }
}

provider "vault" {
  address = "http://192.168.1.138:8200"

  auth_login {
    path   = "auth/jwt/login"
    method = "jwt"

    parameters = {
      "role" = "brickhill-production-terraform"
      "jwt"  = var.CI_JWT
    }
  }
}

provider "aws" {
  region = "us-east-1"
}

data "vault_generic_secret" "cf_api_token" {
  path = "brickhill/prod/terraform/cloudflare"
}

provider "cloudflare" {
  api_token = data.vault_generic_secret.cf_api_token.data["api_key"]
}

module "vpc" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//vpc?ref=0.0.1"
}

module "sqs" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//sqs?ref=0.0.1"
}

module "acm" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//acm?ref=0.0.1"

  brickhill_domain = "brick-hill.com"
  brkcdn_domain    = "brkcdn.com"
}

module "s3" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//s3?ref=0.0.1"

  env_name = var.env_name
}

module "cloudfront" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//cloudfront?ref=0.0.1"

  acm_domain = "*.brkcdn.com"

  blog_s3_bucket_id       = module.s3.blog_bucket_id
  thumbnails_s3_bucket_id = module.s3.thumbnails_bucket_id
  brkcdn_s3_bucket_id     = module.s3.brkcdn_bucket_id
  css_s3_bucket_id        = module.s3.css_bucket_id
  js_s3_bucket_id         = module.s3.js_bucket_id
  downloads_s3_bucket_id  = module.s3.downloads_bucket_id

  depends_on = [
    module.s3,
    module.acm
  ]
}

module "elasticache" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//elasticache?ref=0.0.1"

  env_name               = var.env_name
  subnet_ids             = module.vpc.public_subnet_ids
  security_group_id      = module.vpc.elasticache_security_group_id
  cluster_instance_type  = "cache.t3.small"
  cluster_instance_count = 1
}

module "database" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//database?ref=0.0.1"

  env_name                   = var.env_name
  subnet_ids                 = module.vpc.public_subnet_ids
  security_group_id          = module.vpc.db_security_group_id
  database_name              = "brickhill"
  cluster_instance_type      = "db.t3.small"
  cluster_instance_count     = 1
  cluster_availability_zones = ["us-east-1d", "us-east-1a", "us-east-1e"]
  deletion_protection        = true
}

module "elb" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//elb?ref=0.0.1"

  env_name   = var.env_name
  subnet_ids = module.vpc.public_subnet_ids
  vpc_id     = module.vpc.vpc_id

  acm_domain  = "*.brick-hill.com"
  blog_domain = "blog.brick-hill.com"

  security_group_id = module.vpc.lb_security_group_id

  depends_on = [
    module.acm
  ]
}

module "iam" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//iam?ref=0.0.1"

  env_name = var.env_name
}

module "ecs" {
  source = "git::https://gitlab.com/brickhill/infrastructure/terraform/modules.git//ecs?ref=0.0.1"

  env_name           = var.env_name
  vpc_id             = module.vpc.vpc_id
  security_group_id  = module.vpc.webserver_security_group_id
  availability_zones = ["us-east-1d"]
  instance_types     = ["t3a.medium", "t3a.large", "t3.medium", "t3.large"]

  target_group_arns = [module.elb.site_group_arn, module.elb.blog_group_arn]

  depends_on = [
    module.iam,
    module.ssm,
    module.elb
  ]
}
