-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Waktu pembuatan: 23 Sep 2025 pada 15.32
-- Versi server: 8.0.30
-- Versi PHP: 8.4.8

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `heaven_spot_indo_db`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `cache`
--

INSERT INTO `cache` (`key`, `value`, `expiration`) VALUES
('heaven-spot-indo-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3', 'i:1;', 1752838319),
('heaven-spot-indo-cache-livewire-rate-limiter:a17961fa74e9275d529f489537f179c05d50c2f3:timer', 'i:1752838319;', 1752838319);

-- --------------------------------------------------------

--
-- Struktur dari tabel `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `categories`
--

CREATE TABLE `categories` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `categories`
--

INSERT INTO `categories` (`id`, `name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Cat Tembok', 'Cat untuk dinding interior dan eksterior', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(2, 'Cat Kayu', 'Cat khusus untuk furniture kayu', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(3, 'Cat Besi', 'Cat anti karat untuk logam', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(4, 'Cat Primer', 'Cat dasar sebelum finishing', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(5, 'Cat Semprot', 'Cat dalam kemasan spray', '2025-07-17 06:03:17', '2025-07-17 06:03:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `customers`
--

CREATE TABLE `customers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `phone` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `customers`
--

INSERT INTO `customers` (`id`, `name`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'Toko Bangunan Sumber Rejeki', '021-7777-0001', 'sumberrejeki@gmail.com', 'Jl. Pasar Minggu No. 111, Jakarta Selatan', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(2, 'CV Mitra Konstruksi', '021-7777-0002', 'mitra@konstruksi.com', 'Jl. Sudirman No. 222, Jakarta Pusat', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(3, 'Kontraktor Bangunan Jaya', '021-7777-0003', 'jaya@kontraktor.com', 'Jl. Thamrin No. 333, Jakarta Pusat', '2025-07-17 06:03:17', '2025-07-17 06:03:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint UNSIGNED NOT NULL,
  `reserved_at` int UNSIGNED DEFAULT NULL,
  `available_at` int UNSIGNED NOT NULL,
  `created_at` int UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000001_create_cache_table', 1),
(2, '0001_01_01_000002_create_jobs_table', 1),
(3, '2025_07_17_115102_create_users_table', 1),
(4, '2025_07_17_115207_create_categories_table', 1),
(5, '2025_07_17_115242_create_suppliers_table', 1),
(6, '2025_07_17_115301_create_customers_table', 1),
(7, '2025_07_17_115334_create_products_table', 1),
(8, '2025_07_17_115407_create_purchase_orders_table', 1),
(9, '2025_07_17_115436_create_purchase_order_items_table', 1),
(10, '2025_07_17_115501_create_sales_orders_table', 1),
(11, '2025_07_17_115523_create_sales_order_items_table', 1),
(12, '2025_07_17_115555_create_stock_movements_table', 1),
(13, '2025_07_17_125310_create_sessions_table', 1),
(14, '2025_07_17_153745_add_tax_to_purchase_orders_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `products`
--

CREATE TABLE `products` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `code` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `category_id` bigint UNSIGNED NOT NULL,
  `brand` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `color` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `size` varchar(10) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unit` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `minimum_stock` int NOT NULL DEFAULT '0',
  `current_stock` int NOT NULL DEFAULT '0',
  `purchase_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `selling_price` decimal(10,2) NOT NULL DEFAULT '0.00',
  `description` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `products`
--

INSERT INTO `products` (`id`, `name`, `code`, `category_id`, `brand`, `color`, `size`, `unit`, `minimum_stock`, `current_stock`, `purchase_price`, `selling_price`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Mowilex Emulsion Paint', 'MWX-001', 1, 'Mowilex', 'Putih', '2.5L', 'Kaleng', 10, 21, 85000.00, 95000.00, 'Cat tembok berkualitas tinggi', '2025-07-17 06:03:17', '2025-07-17 17:11:58'),
(2, 'Avitex Wall Paint', 'AVT-002', 1, 'Avitex', 'Biru', '1L', 'Kaleng', 15, 23, 45000.00, 50000.00, 'Cat tembok ekonomis berkualitas', '2025-07-17 06:03:17', '2025-07-17 17:08:15'),
(3, 'Wood Stain Natural', 'WS-003', 2, 'Biovarnish', 'Natural', '1L', 'Kaleng', 8, 19, 65000.00, 75000.00, 'Pewarna kayu alami', '2025-07-17 06:03:17', '2025-07-17 14:49:10'),
(4, 'Anti Rust Paint', 'AR-004', 3, 'Rust Guard', 'Merah', '1L', 'Kaleng', 12, 33, 55000.00, 65000.00, 'Cat anti karat untuk logam', '2025-07-17 06:03:17', '2025-07-17 17:18:04'),
(5, 'Primer Sealer', 'PR-005', 4, 'Base Coat', 'Transparan', '1L', 'Kaleng', 10, 15, 40000.00, 48000.00, 'Primer dasar untuk finishing', '2025-07-17 06:03:17', '2025-07-17 06:03:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_orders`
--

CREATE TABLE `purchase_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `po_number` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `supplier_id` bigint UNSIGNED NOT NULL,
  `purchase_date` date NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `subtotal` decimal(12,2) NOT NULL DEFAULT '0.00',
  `tax_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `tax_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `discount_percentage` decimal(5,2) NOT NULL DEFAULT '0.00',
  `discount_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `grand_total` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `purchase_orders`
--

INSERT INTO `purchase_orders` (`id`, `po_number`, `supplier_id`, `purchase_date`, `total_amount`, `subtotal`, `tax_percentage`, `tax_amount`, `discount_percentage`, `discount_amount`, `grand_total`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(1, 'PO-20250717-001', 2, '2025-07-17', 499500.00, 450000.00, 11.00, 49500.00, 0.00, 0.00, 499500.00, 'completed', 'Testing', '2025-07-17 08:18:17', '2025-07-17 08:54:19'),
(2, 'PO-20250717-002', 2, '2025-07-17', 252000.00, 225000.00, 12.00, 27000.00, 0.00, 0.00, 252000.00, 'completed', 'testingg', '2025-07-17 16:13:39', '2025-07-17 16:14:21'),
(3, 'PO-20250717-003', 3, '2025-07-17', 344100.00, 310000.00, 11.00, 34100.00, 0.00, 0.00, 344100.00, 'completed', 'test', '2025-07-17 16:53:51', '2025-07-17 17:02:05'),
(4, 'PO-20250718-001', 2, '2025-07-18', 1221000.00, 1100000.00, 11.00, 121000.00, 0.00, 0.00, 1221000.00, 'completed', 'ayam', '2025-07-17 17:17:15', '2025-07-17 17:18:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `purchase_order_items`
--

CREATE TABLE `purchase_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `purchase_order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `purchase_order_items`
--

INSERT INTO `purchase_order_items` (`id`, `purchase_order_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 10, 45000.00, 450000.00, '2025-07-17 08:26:44', '2025-07-17 08:26:44'),
(2, 2, 2, 5, 45000.00, 225000.00, '2025-07-17 16:14:21', '2025-07-17 16:14:21'),
(3, 3, 1, 1, 85000.00, 85000.00, '2025-07-17 16:54:52', '2025-07-17 16:54:52'),
(5, 4, 4, 20, 55000.00, 1100000.00, '2025-07-17 17:18:04', '2025-07-17 17:18:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales_orders`
--

CREATE TABLE `sales_orders` (
  `id` bigint UNSIGNED NOT NULL,
  `so_number` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `customer_id` bigint UNSIGNED NOT NULL,
  `sale_date` date NOT NULL,
  `total_amount` decimal(12,2) NOT NULL DEFAULT '0.00',
  `status` enum('pending','completed','cancelled') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'pending',
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sales_orders`
--

INSERT INTO `sales_orders` (`id`, `so_number`, `customer_id`, `sale_date`, `total_amount`, `status`, `notes`, `created_at`, `updated_at`) VALUES
(2, 'SO-20250717-001', 2, '2025-07-17', 260000.00, 'completed', 'testing', '2025-07-17 12:44:52', '2025-07-17 14:49:10'),
(3, 'SO-20250717-002', 3, '2025-07-28', 575000.00, 'completed', 'test 3', '2025-07-17 13:57:54', '2025-07-17 15:08:26'),
(4, 'SO-20250717-003', 3, '2025-07-17', 285000.00, 'completed', 'test 5', '2025-07-17 16:11:41', '2025-07-17 16:12:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sales_order_items`
--

CREATE TABLE `sales_order_items` (
  `id` bigint UNSIGNED NOT NULL,
  `sales_order_id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `quantity` int NOT NULL,
  `unit_price` decimal(10,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sales_order_items`
--

INSERT INTO `sales_order_items` (`id`, `sales_order_id`, `product_id`, `quantity`, `unit_price`, `total_price`, `created_at`, `updated_at`) VALUES
(2, 2, 2, 1, 50000.00, 50000.00, '2025-07-17 13:21:29', '2025-07-17 13:21:29'),
(3, 3, 2, 5, 50000.00, 250000.00, '2025-07-17 13:58:28', '2025-07-17 13:58:28'),
(4, 2, 1, 2, 75000.00, 150000.00, '2025-07-17 14:22:10', '2025-07-17 14:22:10'),
(5, 2, 3, 1, 60000.00, 60000.00, '2025-07-17 14:49:10', '2025-07-17 14:49:10'),
(6, 3, 4, 5, 65000.00, 325000.00, '2025-07-17 15:06:31', '2025-07-17 15:06:31'),
(7, 4, 1, 3, 95000.00, 285000.00, '2025-07-17 16:12:21', '2025-07-17 16:12:21');

-- --------------------------------------------------------

--
-- Struktur dari tabel `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('GxmyNxlB62ETEPPjOyGQ0o7kDMwefflBexT9BiOX', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiSkFrZjJzM1FMREh4Q1FXNDNhaUdxV3JTd2lFVngwUFdhRzR2QTZUbSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1752837629),
('I7FZkEpvky7ahlpML2piKz0XZfrOuAvIBRGPC4df', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiWmZ1N0JOeEhDcEE0NHdIVDByZXJ2UE5wajhSM1kzZ0xaYzh0TzF3VCI7czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6NjoiX2ZsYXNoIjthOjI6e3M6Mzoib2xkIjthOjA6e31zOjM6Im5ldyI7YTowOnt9fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fX0=', 1752838359),
('KXGqMQhYYqvb1DjXcfkf7fYeWIcBc6MddCHY98kh', 1, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo3OntzOjY6Il90b2tlbiI7czo0MDoiMGFCQ1BWTzdBbHprVTQ5ckZuVUoyOHhUaEFpZzhYNVlnclhBZzdEcCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6Mzg6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9jYXRlZ29yaWVzIjt9czozOiJ1cmwiO2E6MDp7fXM6NTA6ImxvZ2luX3dlYl81OWJhMzZhZGRjMmIyZjk0MDE1ODBmMDE0YzdmNThlYTRlMzA5ODlkIjtpOjE7czoxNzoicGFzc3dvcmRfaGFzaF93ZWIiO3M6NjA6IiQyeSQxMiRxV0JTMHBPbHNSL1NySHQ4OFhkMTl1YnFIaE5JUlljVVp2Z3pVNGhjYXc1bUprdDhTZ0pCUyI7czo4OiJmaWxhbWVudCI7YTowOnt9fQ==', 1752847357),
('LSV4ErstYqkBlPCsNz7Lcv8UpVUAFsxQes95zt36', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiUTdYdHVsd1hqUjN5Y2YwS25NTXdDc0J6UklEeXFOMXY3SU1VQ0lxQyI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fXM6MzoidXJsIjthOjE6e3M6ODoiaW50ZW5kZWQiO3M6Mjc6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbiI7fX0=', 1752858897),
('nNBALZPdxoEdtVeRIo2j9TSFms9yVE0EvJUwl1YO', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiS3JrOFgxVThzTzVsdkd5WVNMTjdtMERVWmhHMXRicFp5enA3Z3hmcSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1752856942),
('ubRXkO35BVSwvvRIoay2f1EmgmJEo4ytNeNy7itE', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YTozOntzOjY6Il90b2tlbiI7czo0MDoiTVFRUWpBMTVVWU1wRkhiakFPR1ZCUlJQMFppUHlPZjBaTkR4V2lKUCI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo5OiJfcHJldmlvdXMiO2E6MTp7czozOiJ1cmwiO3M6MzM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9hZG1pbi9sb2dpbiI7fX0=', 1752837454),
('vLpdNcAmz0l3FzNIORpd66pJSv4l7n4F8ElQS1J7', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoia2ROcWIwTldYOUh6UTdTbkpjMWJmRTlDV1dHZ2pVS0NDRmJtR1A0cSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1752837404),
('yQarTOz0h9TfB6wlhhw7wiwl1zhIC0ECs5AsLbCy', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/138.0.0.0 Safari/537.36', 'YToyOntzOjY6Il90b2tlbiI7czo0MDoiRHU3TExKeUpMWGpRTElSazRwVXZkRGJqWGFMbTIwdFJGc1dBTVVoNSI7czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319fQ==', 1752837860);

-- --------------------------------------------------------

--
-- Struktur dari tabel `stock_movements`
--

CREATE TABLE `stock_movements` (
  `id` bigint UNSIGNED NOT NULL,
  `product_id` bigint UNSIGNED NOT NULL,
  `type` enum('in','out','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_type` enum('purchase','sale','adjustment') COLLATE utf8mb4_unicode_ci NOT NULL,
  `reference_id` int DEFAULT NULL,
  `quantity` int NOT NULL,
  `previous_stock` int NOT NULL,
  `current_stock` int NOT NULL,
  `notes` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `stock_movements`
--

INSERT INTO `stock_movements` (`id`, `product_id`, `type`, `reference_type`, `reference_id`, `quantity`, `previous_stock`, `current_stock`, `notes`, `created_at`, `updated_at`) VALUES
(1, 2, 'out', 'sale', 2, 1, 30, 29, 'Penjualan ke CV Mitra Konstruksi - SO: SO-20250717-001 - Produk: Avitex Wall Paint', '2025-07-17 13:42:47', '2025-07-17 13:42:47'),
(2, 2, 'out', 'sale', 3, 5, 29, 24, 'Penjualan ke Toko Bangunan Sumber Rejeki - SO: SO-20250717-002 - Produk: Avitex Wall Paint', '2025-07-17 14:14:44', '2025-07-17 14:14:44'),
(3, 2, 'out', 'sale', 2, 1, 24, 23, 'Penjualan ke CV Mitra Konstruksi - SO: SO-20250717-001 - Produk: Avitex Wall Paint', '2025-07-17 14:22:12', '2025-07-17 14:22:12'),
(4, 1, 'out', 'sale', 2, 2, 25, 23, 'Manual fix - Item ditambah ke SO completed - SO: SO-20250717-001', '2025-07-17 14:29:36', '2025-07-17 14:29:36'),
(5, 3, 'out', 'sale', 2, 1, 20, 19, 'Item ditambah ke SO completed - SO: SO-20250717-001 - Produk: Wood Stain Natural', '2025-07-17 14:49:10', '2025-07-17 14:49:10'),
(6, 4, 'out', 'sale', 3, 5, 18, 13, 'Item ditambah ke SO completed - SO: SO-20250717-002 - Produk: Anti Rust Paint', '2025-07-17 16:05:15', '2025-07-17 16:05:15'),
(7, 1, 'out', 'sale', 4, 3, 23, 20, 'Item ditambah ke SO completed - SO: SO-20250717-003 - Produk: Mowilex Emulsion Paint', '2025-07-17 16:12:21', '2025-07-17 16:12:21'),
(8, 1, 'in', 'purchase', 3, 1, 20, 21, 'Pembelian dari Toko Cat Jaya - PO: PO-20250717-003 - Produk: Mowilex Emulsion Paint', '2025-07-17 16:55:28', '2025-07-17 16:55:28'),
(9, 1, 'in', 'purchase', 3, 1, 21, 22, 'Pembelian dari Toko Cat Jaya - PO: PO-20250717-003 - Produk: Mowilex Emulsion Paint', '2025-07-17 16:55:29', '2025-07-17 16:55:29'),
(10, 2, 'in', 'purchase', 3, 5, 23, 28, 'Item ditambah ke PO completed - PO: PO-20250717-003 - Produk: Avitex Wall Paint', '2025-07-17 17:02:05', '2025-07-17 17:02:05'),
(11, 2, 'out', 'adjustment', 3, 5, 28, 23, 'Item dihapus dari PO completed - PO: PO-20250717-003 - Produk: Avitex Wall Paint', '2025-07-17 17:08:15', '2025-07-17 17:08:15'),
(12, 1, 'out', 'adjustment', 3, 1, 22, 21, 'Rollback pembelian (PO dihapus/dibatalkan) - PO: PO-20250717-003 - Produk: Mowilex Emulsion Paint', '2025-07-17 17:11:59', '2025-07-17 17:11:59'),
(13, 4, 'in', 'purchase', 4, 20, 13, 33, 'Item ditambah ke PO completed - PO: PO-20250718-001 - Produk: Anti Rust Paint', '2025-07-17 17:18:04', '2025-07-17 17:18:04');

-- --------------------------------------------------------

--
-- Struktur dari tabel `suppliers`
--

CREATE TABLE `suppliers` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `contact_person` varchar(20) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `phone` varchar(15) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `address` text COLLATE utf8mb4_unicode_ci,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `suppliers`
--

INSERT INTO `suppliers` (`id`, `name`, `contact_person`, `phone`, `email`, `address`, `created_at`, `updated_at`) VALUES
(1, 'PT Mowilex Indonesia', 'Budi Santoso', '021-5555-0001', 'budi@mowilex.com', 'Jl. Industri No. 123, Jakarta', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(2, 'CV Avitex Paint', 'Siti Rahayu', '021-5555-0002', 'siti@avitex.com', 'Jl. Raya Bogor No. 456, Depok', '2025-07-17 06:03:17', '2025-07-17 06:03:17'),
(3, 'Toko Cat Jaya', 'Ahmad Wijaya', '021-5555-0003', 'ahmad@catjaya.com', 'Jl. Kemang Raya No. 789, Jakarta Selatan', '2025-07-17 06:03:17', '2025-07-17 06:03:17');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `id` bigint UNSIGNED NOT NULL,
  `name` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL,
  `role` enum('admin') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'admin',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `email_verified_at`, `password`, `role`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin@heaven-spot-indo.com', NULL, '$2y$12$REPLACE_WITH_YOUR_OWN_BCRYPT_HASH_XXXXXXXXXXXXXXXXXXXXXXXX', 'admin', NULL, '2025-07-17 06:03:17', '2025-07-17 06:03:17');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indeks untuk tabel `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `products_code_unique` (`code`),
  ADD KEY `products_category_id_foreign` (`category_id`);

--
-- Indeks untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `purchase_orders_po_number_unique` (`po_number`),
  ADD KEY `purchase_orders_supplier_id_foreign` (`supplier_id`);

--
-- Indeks untuk tabel `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `purchase_order_items_purchase_order_id_foreign` (`purchase_order_id`),
  ADD KEY `purchase_order_items_product_id_foreign` (`product_id`);

--
-- Indeks untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `sales_orders_so_number_unique` (`so_number`),
  ADD KEY `sales_orders_customer_id_foreign` (`customer_id`);

--
-- Indeks untuk tabel `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sales_order_items_sales_order_id_foreign` (`sales_order_id`),
  ADD KEY `sales_order_items_product_id_foreign` (`product_id`);

--
-- Indeks untuk tabel `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indeks untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `stock_movements_product_id_foreign` (`product_id`);

--
-- Indeks untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `categories`
--
ALTER TABLE `categories`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `customers`
--
ALTER TABLE `customers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT untuk tabel `products`
--
ALTER TABLE `products`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `sales_order_items`
--
ALTER TABLE `sales_order_items`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT untuk tabel `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `products_category_id_foreign` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `purchase_orders`
--
ALTER TABLE `purchase_orders`
  ADD CONSTRAINT `purchase_orders_supplier_id_foreign` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `purchase_order_items`
--
ALTER TABLE `purchase_order_items`
  ADD CONSTRAINT `purchase_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `purchase_order_items_purchase_order_id_foreign` FOREIGN KEY (`purchase_order_id`) REFERENCES `purchase_orders` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sales_orders`
--
ALTER TABLE `sales_orders`
  ADD CONSTRAINT `sales_orders_customer_id_foreign` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `sales_order_items`
--
ALTER TABLE `sales_order_items`
  ADD CONSTRAINT `sales_order_items_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `sales_order_items_sales_order_id_foreign` FOREIGN KEY (`sales_order_id`) REFERENCES `sales_orders` (`id`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `stock_movements`
--
ALTER TABLE `stock_movements`
  ADD CONSTRAINT `stock_movements_product_id_foreign` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
