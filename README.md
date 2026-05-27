# ☁️ Giải Pháp HA Cluster Trên AWS

<div align="center">

![AWS](https://img.shields.io/badge/AWS-ECS%20Fargate-FF9900?style=for-the-badge&logo=amazon-aws&logoColor=white)
![Docker](https://img.shields.io/badge/Docker-Container-2496ED?style=for-the-badge&logo=docker&logoColor=white)
![PHP](https://img.shields.io/badge/PHP-8.2-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![GitHub Actions](https://img.shields.io/badge/CI%2FCD-GitHub_Actions-2088FF?style=for-the-badge&logo=github-actions&logoColor=white)

**Triển khai hệ thống web application PHP với kiến trúc High Availability Cluster trên AWS,**
**đảm bảo tự động failover khi cluster chính gặp sự cố.**

 **Demo Live:** [doanhoitdc.id.vn](http://doanhoitdc.id.vn)

</div>

---

##  Vấn Đề Cần Giải Quyết

Hệ thống web truyền thống chạy trên **1 server duy nhất** gặp các vấn đề:

```
- Single Point of Failure: Server chết → Web chết hoàn toàn
- Downtime khi deploy: Phải tắt server để cập nhật code
- Không scale được: Traffic tăng → Server quá tải
 Mất dữ liệu: Không có backup tự động
```

**Giải pháp:** Triển khai **HA Cluster trên AWS** với khả năng:

```
- Tự động failover < 30 giây khi cluster chính gặp sự cố
- Zero downtime khi deploy version mới
- Tự động scale theo traffic
- Backup database tự động mỗi ngày
```

---

##  Kiến Trúc Cluster

```
┌─────────────────────────────────────────────────────────────┐
│                        INTERNET                             │
│                           │                                 │
│               doanhoitdc.id.vn (DNS CNAME)                  │
└───────────────────────────┼─────────────────────────────────┘
                            │
┌───────────────────────────▼─────────────────────────────────┐
│              Application Load Balancer                      │
│         ha-web-alb · Internet-facing · HTTP:80              │
│     Health Check: /health.php · Round Robin                 │
│          Tự động phát hiện cluster lỗi → reroute            │
└──────────────────┬──────────────────┬───────────────────────┘
                   │                  │
┌──────────────────▼──┐  ┌────────────▼──────────────────────┐
│   CLUSTER CHÍNH     │  │        CLUSTER PHỤ                │
│   ECS Task 1        │  │        ECS Task 2                 │
│   PHP + Apache      │  │        PHP + Apache               │
│   AZ: ap-southeast  │  │        AZ: ap-southeast           │
│   1a · 1vCPU/3GB    │  │        1b · 1vCPU/3GB             │
│   ● RUNNING         │  │        ● RUNNING                  │
└──────────┬──────────┘  └───────────────┬───────────────────┘
           │                             │
┌──────────▼─────────────────────────────▼───────────────────┐
│                    VPC: 10.0.0.0/16                         │
│              Singapore (ap-southeast-1)                     │
│                                                             │
│  ┌─────────────────────────────────────────────────────┐   │
│  │              RDS MySQL 8.0                          │   │
│  │   ha-db · db.t3.micro · 20GB · Backup 7 ngày       │   │
│  └─────────────────────────────────────────────────────┘   │
│                                                             │
│  Public Subnets  (AZ-1a, AZ-1b) → ALB                     │
│  Private Subnets (AZ-1a, AZ-1b) → ECS Tasks               │
│  NAT Gateway · Internet Gateway · Security Groups          │
└─────────────────────────────────────────────────────────────┘
```

---

##  Cơ Chế Tự Động Failover

### Khi Cluster Chính (Task 1) Gặp Sự Cố

```
T+0s  ── Cluster 1 (AZ-1a) crash đột ngột
T+10s ── ALB health check thất bại → loại Cluster 1 khỏi pool
         100% traffic chuyển → Cluster 2 (AZ-1b)
          User KHÔNG thấy lỗi, web vẫn chạy!
T+30s ── ECS tự động tạo Cluster 1 mới
T+60s ── Cluster mới HEALTHY → 2/2 running 
```

### Khi Cả AZ-1a Gặp Sự Cố

```
T+0s  ── AZ-1a mất kết nối hoàn toàn
T+10s ── ALB reroute 100% → Cluster 2 (AZ-1b)
T+90s ── ECS tạo thêm cluster ở AZ-1b
         Hệ thống tiếp tục hoạt động 
```

---

##  Thành Phần Hệ Thống

### Security Groups (Least Privilege)

```
sg-alb  ←── Internet    : Port 80, 443
sg-ecs  ←── sg-alb only : Port 80
sg-rds  ←── sg-ecs only : Port 3306
```

### ECS Fargate Cluster

| Thông số | Giá trị |
|---------|---------|
| Cluster | webapp-cluster |
| Desired Tasks | 2 (Multi-AZ) |
| CPU / Memory | 1 vCPU / 3 GB |
| Launch Type | Fargate (Serverless) |

### Auto Scaling Policy

| Metric | Scale Out | Min | Max |
|--------|-----------|-----|-----|
| CPU Utilization | > 70% | 2 | 6 |
| Memory Utilization | > 75% | 2 | 6 |
| Request/Target | > 1000/min | 2 | 6 |

---

##  CI/CD Pipeline

```
git push origin main
        │
        ▼
  GitHub Actions
        │
   1. docker build (image mới)
   2. docker push → Docker Hub
   3. Update ECS Task Definition
   4. Rolling Update (Zero Downtime)
        │
        ▼
    Live trong ~2 phút
```

---

##  Cài Đặt

### Chạy Local

```bash
git clone https://github.com/Cndinh3124/ha-cluster-aws.git
cd ha-cluster-aws
docker-compose up -d
# Truy cập: http://localhost
```

### GitHub Secrets Cần Thiết

```
AWS_ACCESS_KEY_ID      = <your-access-key>
AWS_SECRET_ACCESS_KEY  = <your-secret-key>
DOCKER_USERNAME        = <docker-hub-username>
DOCKER_PASSWORD        = <docker-hub-password>
```

### Environment Variables (ECS)

```
DB_HOST = <rds-endpoint>.rds.amazonaws.com
DB_NAME = hoi_sinh_vien
DB_USER = admin
DB_PASS = <password>
APP_URL = (để trống)
```

---

##  Kết Quả

| Tiêu chí | Kết quả |
|---------|---------|
| Clusters running | 2/2 (Multi-AZ) |
| Failover time | < 60 giây |
| Deploy time | ~2 phút |
| Auto scaling | 2 → 6 clusters |
| DB backup | 7 ngày tự động |
| Uptime target | 99.9% |

---

##  Cấu Trúc Repository

```
ha-cluster-aws/
├── .github/workflows/
│   └── deploy.yml          # CI/CD Pipeline
├── config/
│   ├── app.php             # APP_URL (env var)
│   └── database.php        # DB config (env vars)
├── admin/                  # Admin panel
├── assets/                 # CSS, Images
├── includes/               # Header, Footer, Functions
├── uploads/                # Media files
├── health.php              # ALB Health Check endpoint
├── hoi_sinh_vien.sql       # Database schema
├── Dockerfile              # Container config
└── docker-compose.yml      # Local development
```

---

##  Chi Phí Ước Tính

| Service | Chi phí/tháng |
|---------|--------------|
| ECS Fargate (2 tasks) | ~$30 |
| RDS MySQL (Free Tier) | $0 |
| ALB | ~$20 |
| NAT Gateway | ~$35 |
| **Tổng** | **~$85/tháng** |

---

##  Thông Tin

| | |
|-|-|
| **Sinh viên** | Nguyễn Công Định |
| **Trường** | Cao Đẳng Công Nghệ Thủ Đức |
| **Ứng dụng** | Cổng thông tin Hội Sinh Viên TDC |
| **Region** | ap-southeast-1 (Singapore) |
| **Live URL** | [doanhoitdc.id.vn](http://doanhoitdc.id.vn) |

---

<div align="center">
Made with on AWS · 2026
</div>
