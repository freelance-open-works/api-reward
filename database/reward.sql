-- phpMyAdmin SQL Dump
-- version 5.3.0-dev+20230111.1d37607132
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Waktu pembuatan: 14 Jan 2023 pada 05.59
-- Versi server: 10.4.24-MariaDB
-- Versi PHP: 8.0.18

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `reward`
--

-- --------------------------------------------------------

--
-- Struktur dari tabel `admins`
--

CREATE TABLE `admins` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'admin', '$2y$12$gBjl.65lVr3l3jIuuTWxouZVKJVhdWdjsSxX.UnSJr07YU11qW6wu', '2021-07-31 16:27:47', '2021-07-31 16:27:52');

-- --------------------------------------------------------

--
-- Struktur dari tabel `catalogue`
--

CREATE TABLE `catalogue` (
  `ID_CATALOGUE` int(11) NOT NULL,
  `ID_CTG_TYPE` int(11) NOT NULL,
  `NAME_CATALOGUE` varchar(250) DEFAULT NULL,
  `DESCRIPTION` varchar(255) NOT NULL,
  `POINT_REQ` int(11) DEFAULT NULL,
  `STOCK` int(11) NOT NULL,
  `STOCK_GUDANG` int(11) NOT NULL DEFAULT 0,
  `PHOTO_SMALL` varchar(250) DEFAULT NULL,
  `PHOTO_MEDIUM` varchar(200) DEFAULT NULL,
  `PHOTO_LARGE` varchar(200) DEFAULT NULL,
  `DESTINATION` varchar(50) NOT NULL,
  `ID_PERIOD` int(11) NOT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `catalogue_type`
--

CREATE TABLE `catalogue_type` (
  `ID_CTG_TYPE` int(11) NOT NULL,
  `CTG_TYPE` varchar(100) DEFAULT NULL,
  `CTG_MAX_REDEEM` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `catalogue_type`
--

INSERT INTO `catalogue_type` (`ID_CTG_TYPE`, `CTG_TYPE`, `CTG_MAX_REDEEM`) VALUES
(1, 'Grand Prize', '1'),
(2, 'Normal', 'unlimited');

-- --------------------------------------------------------

--
-- Struktur dari tabel `device_version`
--

CREATE TABLE `device_version` (
  `ID_DEVICE_VERSION` int(11) NOT NULL,
  `DEVICE` varchar(100) DEFAULT NULL,
  `VERSION` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `device_version`
--

INSERT INTO `device_version` (`ID_DEVICE_VERSION`, `DEVICE`, `VERSION`) VALUES
(1, 'android', '3'),
(2, 'ios', '3');

-- --------------------------------------------------------

--
-- Struktur dari tabel `elearning_challenge`
--

CREATE TABLE `elearning_challenge` (
  `ID_ELEARNING_CHALLENGE` int(11) NOT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `DESC` varchar(200) DEFAULT NULL,
  `ID_CATEGORY` int(11) DEFAULT NULL,
  `MODULE` varchar(50) DEFAULT NULL,
  `ACTION` varchar(50) DEFAULT NULL,
  `CATEGORY` varchar(200) DEFAULT NULL,
  `ID_EVENTNAME` int(11) DEFAULT NULL,
  `EVENTNAME_DISPLAY` varchar(200) DEFAULT NULL,
  `EVENTNAME_CODE` varchar(255) DEFAULT NULL,
  `POINT` int(11) NOT NULL,
  `DESTINATION` varchar(50) NOT NULL,
  `MAX_COUNT` int(11) DEFAULT NULL,
  `ID_PERIOD` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `elearning_challenge_backup`
--

CREATE TABLE `elearning_challenge_backup` (
  `ID_ELEARNING_CHALLENGE` int(11) NOT NULL,
  `NAME` varchar(100) DEFAULT NULL,
  `DESC` varchar(200) DEFAULT NULL,
  `ID_CATEGORY` int(11) DEFAULT NULL,
  `MODULE` varchar(50) DEFAULT NULL,
  `ACTION` varchar(50) DEFAULT NULL,
  `CATEGORY` varchar(200) DEFAULT NULL,
  `ID_EVENTNAME` int(11) DEFAULT NULL,
  `EVENTNAME_DISPLAY` varchar(200) DEFAULT NULL,
  `EVENTNAME_CODE` varchar(255) DEFAULT NULL,
  `POINT` int(11) NOT NULL,
  `DESTINATION` varchar(50) NOT NULL,
  `MAX_COUNT` int(11) DEFAULT NULL,
  `ID_PERIOD` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `elearning_history`
--

CREATE TABLE `elearning_history` (
  `ID_ELEARNING_HISTORY` int(11) NOT NULL,
  `ID_USERS` varchar(30) NOT NULL,
  `ID_ELEARNING_CHALLENGE` int(11) NOT NULL,
  `ID_LOG_KULIAH` bigint(20) NOT NULL,
  `INFO` varchar(200) DEFAULT NULL,
  `IP_ADDRESS` varchar(20) NOT NULL,
  `DATE_HISTORY` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `TYPE` varchar(10) NOT NULL,
  `KUL_LOG_OBJECT_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `elearning_history_backup`
--

CREATE TABLE `elearning_history_backup` (
  `ID_ELEARNING_HISTORY` int(11) NOT NULL,
  `ID_USERS` varchar(30) NOT NULL,
  `ID_ELEARNING_CHALLENGE` int(11) NOT NULL,
  `ID_LOG_KULIAH` bigint(20) NOT NULL,
  `INFO` varchar(200) NOT NULL,
  `IP_ADDRESS` varchar(20) NOT NULL,
  `DATE_HISTORY` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `TYPE` varchar(10) NOT NULL,
  `KUL_LOG_OBJECT_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `elearning_history_original`
--

CREATE TABLE `elearning_history_original` (
  `ID_ELEARNING_HISTORY` int(11) NOT NULL,
  `ID_USERS` varchar(30) NOT NULL,
  `ID_ELEARNING_CHALLENGE` int(11) NOT NULL,
  `ID_LOG_KULIAH` bigint(20) NOT NULL,
  `INFO` varchar(200) NOT NULL,
  `IP_ADDRESS` varchar(20) NOT NULL,
  `DATE_HISTORY` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `TYPE` varchar(10) NOT NULL,
  `KUL_LOG_OBJECT_ID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `events`
--

CREATE TABLE `events` (
  `ID_EVENTS` int(11) NOT NULL,
  `ID_EVENT_TYPE` int(11) NOT NULL,
  `NAME_EVENTS` varchar(200) DEFAULT NULL,
  `DESC_EVENTS` varchar(500) DEFAULT NULL,
  `PHOTO_SMALL` varchar(255) NOT NULL,
  `PHOTO_MEDIUM` varchar(255) NOT NULL,
  `PHOTO_LARGE` varchar(255) NOT NULL,
  `DATE_START` timestamp NULL DEFAULT '0000-00-00 00:00:00',
  `DATE_FINISH` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `DESTINATION` varchar(50) NOT NULL,
  `ID_PERIOD` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `event_detail`
--

CREATE TABLE `event_detail` (
  `ID_EVENT_DETAIL` int(11) NOT NULL,
  `ID_EVENT_ROLE` int(11) NOT NULL,
  `ID_EVENTS` int(11) NOT NULL,
  `POINT` int(11) NOT NULL,
  `ROLE_INFO` varchar(250) DEFAULT ' '
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `event_detail`
--

INSERT INTO `event_detail` (`ID_EVENT_DETAIL`, `ID_EVENT_ROLE`, `ID_EVENTS`, `POINT`, `ROLE_INFO`) VALUES
(32, 2, 8, 20, 'ph'),
(33, 3, 8, 30, '#tw'),
(34, 4, 8, 30, '#fb'),
(35, 5, 8, 0, NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `event_role`
--

CREATE TABLE `event_role` (
  `ID_EVENT_ROLE` int(11) NOT NULL,
  `NAME_EVENT_ROLE` varchar(100) DEFAULT NULL,
  `ROLE_DETAIL` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `event_role`
--

INSERT INTO `event_role` (`ID_EVENT_ROLE`, `NAME_EVENT_ROLE`, `ROLE_DETAIL`) VALUES
(1, 'barcode', 'barcode info'),
(2, 'photo', 'photo info'),
(3, 'twitter', 'twitter info'),
(4, 'facebook', 'facebook info'),
(5, 'instagram', 'instagram info');

-- --------------------------------------------------------

--
-- Struktur dari tabel `event_type`
--

CREATE TABLE `event_type` (
  `ID_EVENT_TYPE` int(11) NOT NULL,
  `EVENT_TYPE` varchar(100) DEFAULT NULL,
  `EVENT_TYPE_DESC` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `event_type`
--

INSERT INTO `event_type` (`ID_EVENT_TYPE`, `EVENT_TYPE`, `EVENT_TYPE_DESC`) VALUES
(1, 'Event Challenge', NULL),
(2, 'IT Lounge Challenge', NULL);

-- --------------------------------------------------------

--
-- Struktur dari tabel `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `helper`
--

CREATE TABLE `helper` (
  `NAME` varchar(150) NOT NULL DEFAULT '',
  `VALUE` varchar(150) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `helper`
--

INSERT INTO `helper` (`NAME`, `VALUE`) VALUES
('MAX_REDEEM', '5'),
('LAST_SYNC_ELEARNING_CHALLENGE_ID', NULL),
('LAST_GEN_OBJID_ELEARN_HIS', '0');

-- --------------------------------------------------------

--
-- Struktur dari tabel `history`
--

CREATE TABLE `history` (
  `ID_HISTORY` int(11) NOT NULL,
  `ID_USERS` varchar(30) DEFAULT NULL,
  `ID_EVENTS` int(11) DEFAULT NULL,
  `DATE_HISTORY` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `history_detail`
--

CREATE TABLE `history_detail` (
  `ID_HISTORY_DETAIL` int(11) NOT NULL,
  `ID_HISTORY` int(11) DEFAULT NULL,
  `ID_EVENT_DETAIL` int(11) DEFAULT NULL,
  `POINT_REACHED` int(11) DEFAULT NULL,
  `HISTORY_DETAIL_INFO` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `kuliah_log_table_ref`
--

CREATE TABLE `kuliah_log_table_ref` (
  `TABLE_NAME` varchar(200) DEFAULT NULL,
  `FIELD_OBJECT_ID` varchar(200) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `kuliah_log_table_ref`
--

INSERT INTO `kuliah_log_table_ref` (`TABLE_NAME`, `FIELD_OBJECT_ID`) VALUES
('blog_association', 'blogid'),
('assignsubmission_file', 'assignment'),
('assign_grades', 'assignment'),
('assign_submission', 'assignment'),
('chat_messages', 'chatid'),
('data_fields', 'dataid'),
('data_records', 'dataid'),
('feedback_completed', 'feedback'),
('quiz_attempts', 'quiz');

-- --------------------------------------------------------

--
-- Struktur dari tabel `last_refresh_elearning_point`
--

CREATE TABLE `last_refresh_elearning_point` (
  `NUMBER` int(11) NOT NULL,
  `ID_USERS` varchar(30) NOT NULL,
  `LAST_TIME` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `maintenance_status`
--

CREATE TABLE `maintenance_status` (
  `STATUS` varchar(30) NOT NULL,
  `UPDATE_TIME` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `maintenance_status`
--

INSERT INTO `maintenance_status` (`STATUS`, `UPDATE_TIME`) VALUES
('active', '2022-01-11 02:26:31');

-- --------------------------------------------------------

--
-- Struktur dari tabel `mdl_event_list`
--

CREATE TABLE `mdl_event_list` (
  `ID_MDL_EVENT_LIST` int(11) NOT NULL,
  `NAME_DISPLAY` varchar(200) NOT NULL,
  `NAME_CODE` varchar(255) NOT NULL,
  `COMPONENT` varchar(150) DEFAULT NULL,
  `EDUCATION_LEVEL` varchar(150) DEFAULT NULL,
  `DATABASE_QUERY_TYPE` varchar(50) DEFAULT NULL,
  `TABLE_AFFECTED` varchar(150) DEFAULT NULL,
  `FIELD_NAME` varchar(150) DEFAULT NULL,
  `SINCE` varchar(50) DEFAULT NULL,
  `LEGACY` varchar(200) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `messages`
--

CREATE TABLE `messages` (
  `id` int(11) NOT NULL,
  `id_sender` int(11) NOT NULL,
  `id_receiver` int(11) NOT NULL,
  `message` text NOT NULL,
  `opened` int(11) NOT NULL DEFAULT 0,
  `device` varchar(15) DEFAULT NULL,
  `version` varchar(10) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_100000_create_password_resets_table', 1),
(2, '2016_06_01_000001_create_oauth_auth_codes_table', 1),
(3, '2016_06_01_000002_create_oauth_access_tokens_table', 1),
(4, '2016_06_01_000003_create_oauth_refresh_tokens_table', 1),
(5, '2016_06_01_000004_create_oauth_clients_table', 1),
(6, '2016_06_01_000005_create_oauth_personal_access_clients_table', 1),
(7, '2019_08_19_000000_create_failed_jobs_table', 1),
(8, '2014_10_12_000000_create_admins_table', 2);

-- --------------------------------------------------------

--
-- Struktur dari tabel `missmatch_elearning_history`
--

CREATE TABLE `missmatch_elearning_history` (
  `ID_MISSMATCH_ELEARNING_HISTORY` int(11) NOT NULL,
  `ID_ELEARNING_HISTORY` int(11) NOT NULL,
  `KUL_LOG_OBJECT_ID` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `news`
--

CREATE TABLE `news` (
  `ID_NEWS` int(11) NOT NULL,
  `NEWS_TITLE` varchar(250) NOT NULL,
  `NEWS_DESCRIPTION` text NOT NULL,
  `PHOTO_SMALL` varchar(250) NOT NULL,
  `PHOTO_MEDIUM` varchar(250) NOT NULL,
  `PHOTO_LARGE` varchar(250) NOT NULL,
  `DATE` timestamp NULL DEFAULT NULL,
  `ID_PERIOD` int(11) DEFAULT NULL,
  `MESSAGE` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `oauth_access_tokens`
--

CREATE TABLE `oauth_access_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `oauth_access_tokens`
--

INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('0163753d266571093b2506cb529f8a1217c95a70d27455bb3f3652e099a3b2c0e2a34511b52d0d72', 1, 3, 'Authentication Token', '[]', 0, '2021-11-10 04:34:32', '2021-11-10 04:34:32', '2022-11-10 11:34:32'),
('034fbf19658c3d36be5e4db2c907f3f14736a14e32b625fdf7699db911a34ca60bf351ce617960c3', 1, 3, 'Authentication Token', '[]', 0, '2021-11-29 02:15:40', '2021-11-29 02:15:40', '2022-11-29 09:15:40'),
('04d6436ac26347add42957eef06fc6f92728a9a9e5d1213f8fcb6edac879b2e7c0dd6eccc1d8ee0b', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 04:57:25', '2021-11-25 04:57:25', '2022-11-25 11:57:25'),
('050fd9e28443d90c2f4500b2f1b7ed71b7323000fd1be3e190f9240564cf48678fc7c18b8d4825c5', 1, 3, 'Authentication Token', '[]', 0, '2021-11-08 04:47:57', '2021-11-08 04:47:57', '2022-11-08 11:47:57'),
('05d843621ee303a7afbcbb37fdb86d7ef623553569edcf646e92988cc9322d3b4723c2dd225db119', 1, 3, 'Authentication Token', '[]', 0, '2021-11-22 02:27:28', '2021-11-22 02:27:28', '2022-11-22 09:27:28'),
('0629113d8c1efddc60eb559f0585d2862f47edf38480473c662b27e9645ce50a5bc652452b767ea7', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 03:44:12', '2023-01-14 03:44:12', '2024-01-14 10:44:12'),
('064cb2270d2781060e339f3808a4faa416bbdfad96228920fb0c8f3baed89fe4ac19a117ef302079', 1, 3, 'Authentication Token', '[]', 0, '2021-11-09 03:28:39', '2021-11-09 03:28:39', '2022-11-09 10:28:39'),
('0704bfa977d66fcf84d3a41ab3f218311438e80e83e296e3d1570b7e15b9c8176a22477f700473df', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 10:15:51', '2023-01-13 10:15:51', '2024-01-13 17:15:51'),
('070f8f21d55d820107fa053f5fa0b5ca0947bad9cc3ddfee125e97b2a8eadd4fa3051294393ac3c6', 1, 3, 'Authentication Token', '[]', 0, '2021-11-22 09:05:26', '2021-11-22 09:05:26', '2022-11-22 16:05:26'),
('07161296eff9d1bc7d8387f1faffd5989eb90a2634027dac33fd05c3f5916bee02e5b5dec2dbcd45', 1, 3, 'Authentication Token', '[]', 0, '2021-11-18 02:18:30', '2021-11-18 02:18:30', '2022-11-18 09:18:30'),
('09106c04827cdb5fd71768c3ed43472b78587053b757d6bd1bf15aaf53543ef8e1b2ff733c6802da', 1, 3, 'Authentication Token', '[]', 0, '2021-11-22 03:12:55', '2021-11-22 03:12:55', '2022-11-22 10:12:55'),
('0964cae00dcda76e27ac632bbc087b0fa26eb8e07b703624be51c7407953b2dde9039131b2df6e47', 1, 3, 'Authentication Token', '[]', 0, '2021-09-02 09:56:24', '2021-09-02 09:56:24', '2022-09-02 16:56:24'),
('09fc3251da2f06bae3434bba721fd63e6cb3aff339cf753c7cdff74410abc37564899c8e7adde3ee', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 10:32:23', '2023-01-13 10:32:23', '2024-01-13 17:32:23'),
('0aef54fb375b4757202c3c81d417f0d413892fc33065827994f88aea112e3f8ec7ea3ccbd0fe3752', 1, 3, 'Authentication Token', '[]', 0, '2021-11-10 03:17:55', '2021-11-10 03:17:55', '2022-11-10 10:17:55'),
('0c097096388f9fe634a7c1255d14a50d0ee862436dc0c008674c16a74a4ed3620566f32e39183202', 1, 3, 'Authentication Token', '[]', 0, '2021-11-24 02:16:59', '2021-11-24 02:16:59', '2022-11-24 09:16:59'),
('0c12762e5aed2d2c58cb0360620acb958e8458ef30ab33c5f52559df2cd847ec9bd3839d4029cbf7', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:21:20', '2021-09-22 04:21:20', '2022-09-22 11:21:20'),
('0c214c91c4390ca61acb9b26803afd04549bb6cfe4f8e4a25873d789555daa42a90c14395f894f04', 1, 3, 'Authentication Token', '[]', 0, '2021-08-19 08:06:24', '2021-08-19 08:06:24', '2022-08-19 15:06:24'),
('0c45b3e02d14062a9d3dd2300d10e6f174c0f638725b358c1393a8afc6054a374674f7b6a5a05aca', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:21:26', '2021-09-22 04:21:26', '2022-09-22 11:21:26'),
('0c4ea423d613042d374e368fa48cc083355ffb90060c61def3938d169c5f86484ccd0e94a5f79133', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 10:01:30', '2023-01-13 10:01:30', '2024-01-13 17:01:30'),
('0e8418721740a9248269d96f94a775c41c6ae6b1fcb05ba43288d26e8e3510f0dd71a77a470e1fb8', 1, 3, 'Authentication Token', '[]', 0, '2021-10-07 07:34:44', '2021-10-07 07:34:44', '2022-10-07 14:34:44'),
('0e96af9c91c3f8b7c2e3835931072458a025cce974f52ed6a8339285c48b24181be258023bdd5b20', 1, 3, 'Authentication Token', '[]', 0, '2021-12-30 09:07:56', '2021-12-30 09:07:56', '2022-12-30 16:07:56'),
('0eb052f63e0ac99a76814261ebd94fefa157c19b3f4a9ba803441776184773dab4be113def8ab83f', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:54', '2021-12-02 07:46:54', '2022-12-02 14:46:54'),
('0ebf69604c90fac000093916e4a4a74ddbb50d1a2dfb0fd33848d493c7ef4ad34a557e4750db69b7', 1, 3, 'Authentication Token', '[]', 0, '2021-12-10 01:50:54', '2021-12-10 01:50:54', '2022-12-10 08:50:54'),
('0ef11830e38cb24e8b0dc1661c39fda189ecd7d73737dd8ef7203d50717b7e78d8b1d4ec9432f7ed', 1, 3, 'Authentication Token', '[]', 0, '2021-10-18 03:30:33', '2021-10-18 03:30:33', '2022-10-18 10:30:33'),
('0f70b2a7d95a2f605a6c6ec646e2be82552394ac87708a506011e1835dc36bcfdfa1371baa296b08', 1, 3, 'Authentication Token', '[]', 0, '2021-11-04 10:01:26', '2021-11-04 10:01:26', '2022-11-04 17:01:26'),
('1122304aaab00c1db7a994437cb41e28f249325f3701e321eb5043d0b19944c82931153be1f139d6', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 04:22:47', '2021-11-25 04:22:47', '2022-11-25 11:22:47'),
('126859723a62a3a6954f32d498a6f286b71c4ef775c8b0749079a6a46154844e7bee573594263d77', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 08:11:45', '2021-12-03 08:11:45', '2022-12-03 15:11:45'),
('13ab592aecb3374b86094d6eba0fe7ec13f89a4580b838121c37c09df45009237fd750e59d6dd777', 1, 3, 'Authentication Token', '[]', 0, '2021-12-13 04:22:27', '2021-12-13 04:22:27', '2022-12-13 11:22:27'),
('140f43d1a9e4b226bc39191b86bbc5c7ec0f5631ca0a89b68d5c7d6e01d8036bcf031d5ef4530b5e', 1, 3, 'Authentication Token', '[]', 0, '2021-11-02 18:33:24', '2021-11-02 18:33:24', '2022-11-03 01:33:24'),
('1492f3ff9651132ac887d68015508c324a978b493a482126a27770a9e518bce772e60be3dfd7fbc7', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 03:39:42', '2021-11-25 03:39:42', '2022-11-25 10:39:42'),
('15355a355a09d0c65a40494658edade0da9f65158406bf0acc4971ee56f17c1eb26a3f8e83824581', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 09:35:53', '2023-01-13 09:35:53', '2024-01-13 16:35:53'),
('155bd80e00a9935c34208e1a0982a8a7d8bf13df852027a8948b728368af921ea9af011887af963a', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 04:40:39', '2022-01-10 04:40:39', '2023-01-10 11:40:39'),
('15d4f22ef120822ab3a61d3b120a89edede9f5290a1368c0bb8b17031055a6467662d20982f17bd9', 1, 3, 'Authentication Token', '[]', 0, '2021-11-23 20:23:55', '2021-11-23 20:23:55', '2022-11-24 03:23:55'),
('16b10133e8177239bea28c2fa47aa16b942f8c0124a7e44bf795c624da349ef3cebd240f982b48fe', 1, 3, 'Authentication Token', '[]', 0, '2021-08-22 19:28:54', '2021-08-22 19:28:54', '2022-08-23 02:28:54'),
('17b1a3c8e831c13001fb4ccda349000ee831d573872a2e1d5126cfcb45765334c258385d0b5c10ca', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 04:15:00', '2022-01-10 04:15:00', '2023-01-10 11:15:00'),
('17d3b0ee6ab74b7fba066a1b02e19f84029c1f1c46d974272ba655f5fce1aa98a45d558bae601a68', 1, 3, 'Authentication Token', '[]', 0, '2021-08-03 06:41:15', '2021-08-03 06:41:15', '2022-08-03 13:41:15'),
('186869ae88393a627df8173dda5a57ae27a7c85ffdd6248f19d797535956ca4905194cc42fcc3113', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:10:33', '2021-09-22 04:10:33', '2022-09-22 11:10:33'),
('19d4c3ef8584417aee0a14e0b81517b4e4deb8cc2fcdc63150d60e08304b9f7c12278e771c97b73b', 1, 3, 'Authentication Token', '[]', 0, '2021-10-29 04:31:57', '2021-10-29 04:31:57', '2022-10-29 11:31:57'),
('1a53b6439d7442a3a102113949190a88d7db4b81d6b833216151f450343fd39d5fb5b869fe0cefce', 1, 3, 'Authentication Token', '[]', 0, '2021-11-22 04:20:49', '2021-11-22 04:20:49', '2022-11-22 11:20:49'),
('1cb88aa4fbd7de272e575fc3df23878ccf1daf2bf0b26feb8f40585bd7cf4fa6ac3293aefd6b91ba', 1, 3, 'Authentication Token', '[]', 0, '2021-11-18 03:27:58', '2021-11-18 03:27:58', '2022-11-18 10:27:58'),
('1da7d21e33fc0d7d2117eb79b933618ea817f8307a9c9d4393dc76cb057c32bd2265d4f9036deec8', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:12:05', '2021-09-22 04:12:05', '2022-09-22 11:12:05'),
('1e07b2fa2e490d01852fcbe5fffe6642f2fae6957bc6769b986af151a7a1d4dc9b90d6bdd388cae1', 1, 3, 'Authentication Token', '[]', 0, '2022-01-11 02:06:03', '2022-01-11 02:06:03', '2023-01-11 09:06:03'),
('1e25bada31ab2a9b13d8da1519851ee0b96cf8c7a7a2b76936546941320fee55250eca353ba99921', 1, 3, 'Authentication Token', '[]', 0, '2021-11-18 02:15:01', '2021-11-18 02:15:01', '2022-11-18 09:15:01'),
('1f01e9f6452ba59bc1b8ba7b6411edfdd20c54bceeaa414eb122296734d945d9d41a98535b98256e', 1, 3, 'Authentication Token', '[]', 0, '2022-01-01 08:51:13', '2022-01-01 08:51:13', '2023-01-01 15:51:13'),
('1fd3e4537824b8b825a01b3bf95089661bff18803663a244f886dd2c44d859bf41b8f73bcb778c3e', 1, 3, 'Authentication Token', '[]', 0, '2021-10-27 06:45:13', '2021-10-27 06:45:13', '2022-10-27 13:45:13'),
('20395ced7ff7a8047292b3ea1a9be1d861c7b58315f67c78a5b41a0bd9fe468a3e49e0488abb015a', 1, 3, 'Authentication Token', '[]', 0, '2021-10-08 05:57:05', '2021-10-08 05:57:05', '2022-10-08 12:57:05'),
('21110048fadd379aa9c2e88b7dda974d22e76ef6593e3108f989208a9e1676a74c447521c9360457', 1, 3, 'Authentication Token', '[]', 0, '2022-01-01 09:19:39', '2022-01-01 09:19:39', '2023-01-01 16:19:39'),
('2460970ca5c393cff400200048eaf88771672405b0bcd1c16a02e3aa1c4baa4cf198925eda3ba52d', 1, 3, 'Authentication Token', '[]', 0, '2021-10-11 07:04:41', '2021-10-11 07:04:41', '2022-10-11 14:04:41'),
('25205967719c3ec031f8aed4c750d979daa8d852d55b07b51a65ca1e98e2d55b6451385fee1238ee', 1, 3, 'Authentication Token', '[]', 0, '2022-01-09 11:55:13', '2022-01-09 11:55:13', '2023-01-09 18:55:13'),
('258479327da746734b450543c0ff2e68e43aca9e0d7f0e51f0f16ac826f5b4de05cbe621f524f828', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 02:47:01', '2022-01-10 02:47:01', '2023-01-10 09:47:01'),
('25884c4a4b31f12529c33ecfe0a90323bd9e3a23f5aa41336d2507d1d4a3207d0f5618d128c44c97', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 04:22:55', '2023-01-14 04:22:55', '2024-01-14 11:22:55'),
('26069fa59e80cfe933d4158843812842c1cb00328462f95ad6bee76537080e61365cb569057aa021', 1, 3, 'Authentication Token', '[]', 0, '2021-11-17 06:36:09', '2021-11-17 06:36:09', '2022-11-17 13:36:09'),
('27640e78a972d5ce369c0726ac143e8d15afab1b2ecc669dd85179169651c79fc734e05d3ede9d31', 1, 3, 'Authentication Token', '[]', 0, '2021-11-03 03:20:42', '2021-11-03 03:20:42', '2022-11-03 10:20:42'),
('282437c382c704917d98a0d50c567995fbde378ac098289106006329a37eb0cd803ea02e367feaef', 1, 3, 'Authentication Token', '[]', 0, '2022-01-06 18:07:27', '2022-01-06 18:07:27', '2023-01-07 01:07:27'),
('2c4c2a832d988dbfd906920d740b02e9fe5494df7084eb78dad4387766ea22c53f887d4d9db141af', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:53', '2021-12-02 07:46:53', '2022-12-02 14:46:53'),
('2c5a9108240d97bc07fd840b7203c6265c659fe4acab16a00c4b759ba63ed27dca6e4afdd7fe1108', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 17:35:21', '2021-12-03 17:35:21', '2022-12-04 00:35:21'),
('2ccb0beca7b136f37998a91f8915cac0dd49df87554d099668c36a833844f0985b963d152fbfb7ba', 1, 3, 'Authentication Token', '[]', 0, '2021-09-29 03:29:54', '2021-09-29 03:29:54', '2022-09-29 10:29:54'),
('2d2de166f832ce6294fc303810d2a46c5afea0aa80442addb66032ec47ed9e29598f6707a284328b', 1, 3, 'Authentication Token', '[]', 0, '2021-10-04 14:35:42', '2021-10-04 14:35:42', '2022-10-04 21:35:42'),
('2ef2c93f510457ad614f1364f0d40a799bfafdd542544ec7c92766a5b13661a19e30d3fa2c7a8a77', 1, 3, 'Authentication Token', '[]', 0, '2021-12-07 04:32:38', '2021-12-07 04:32:38', '2022-12-07 11:32:38'),
('3079c5380d87d97d3f3784f0ea8a40e1de02438150e1c86bdbed8a57fb99b9a67727136478df5704', 1, 3, 'Authentication Token', '[]', 0, '2021-10-12 05:24:35', '2021-10-12 05:24:35', '2022-10-12 12:24:35'),
('3117841cf1e297d27121dc950492c7459db5427c5fc3eb100caf5f3f6c298361ddd211f8f889195f', 1, 3, 'Authentication Token', '[]', 0, '2022-01-07 06:07:13', '2022-01-07 06:07:13', '2023-01-07 13:07:13'),
('3122a6ab1cb48a8d4ea1241c087b73189fa460ae525796b5a42ecda4c7f97016fa3b50d67613be96', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:12:38', '2021-09-22 04:12:38', '2022-09-22 11:12:38'),
('3141a143723550f2fed9ada40d620486fb6a71bdee1feff7131660c40b9b7497b91a24fed17ef629', 1, 3, 'Authentication Token', '[]', 0, '2021-09-26 20:39:27', '2021-09-26 20:39:27', '2022-09-27 03:39:27'),
('33425ac820895e58dc6cc923374a540d6e8f44044960043aa27648ac50546a0ff1800b75eb407237', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 09:30:55', '2021-12-02 09:30:55', '2022-12-02 16:30:55'),
('336750cd40f7354465f3eb1b319d389423c45de47cee479a7284732b38611bf66381c351f9dd552a', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:04:10', '2021-09-13 23:04:10', '2021-09-15 06:04:09'),
('341c93ccae4cef842f9e4121443f66657996a9ab38e746d095787738ea0b9c3a29e021124be1c8d9', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:53', '2021-12-02 07:46:53', '2022-12-02 14:46:53'),
('352ed1866485c8f8b9a11de56b9dbb06a0b58bec49d9c7ffc0398f57a1552f098d6d018b9eb5b614', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 07:40:33', '2023-01-13 07:40:33', '2024-01-13 14:40:33'),
('3757dc4ba3ef379d5cf7e822015fe1887a4d771defa6ddd5209847c42c23a0ad73c6679bbc259d84', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:21:20', '2021-09-22 04:21:20', '2022-09-22 11:21:20'),
('37af68cd00a8293b2e86a19da572427bcfa41f919f353e5fddd1fb66a825329ffc107f2308f0e5dd', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 09:20:58', '2021-12-02 09:20:58', '2022-12-02 16:20:58'),
('395992901f992cf4f13fa655b0cb630ac33bf22d099fe7e165a0b92e1707396db43d5279423bc1c6', 1, 3, 'Authentication Token', '[]', 0, '2022-01-03 09:19:08', '2022-01-03 09:19:08', '2023-01-03 16:19:08'),
('3959a6553b82dd1fe959738d46ed58c8a8503b03a3c51e8d89f2a6d2317bd5d70d841c3a1c7c0693', 1, 3, 'Authentication Token', '[]', 0, '2021-08-09 00:08:31', '2021-08-09 00:08:31', '2022-08-09 07:08:31'),
('3c097b3c4d0e42c82baf38632d209277b490196cc18df25202473d3e9ba98b9004c0dd117d2cb954', 1, 3, 'Authentication Token', '[]', 0, '2021-08-03 06:41:14', '2021-08-03 06:41:14', '2022-08-03 13:41:14'),
('3ee3fad9c2354f2008ca16705d7f6cd86ff432a79e3f459acec3e7d26783113a6ca1d2b9d9c30562', 1, 3, 'Authentication Token', '[]', 0, '2022-01-03 21:05:07', '2022-01-03 21:05:07', '2023-01-04 04:05:07'),
('3f2de904057c473804d27206aaaf1d65a125ac963a68a8b15e11cc97480fdb342a71d1346296a26d', 1, 3, 'Authentication Token', '[]', 0, '2021-10-15 04:55:10', '2021-10-15 04:55:10', '2022-10-15 11:55:10'),
('3fb60cbe09236725b196f7a9f940c1463d3382e332fdee98078b15f3fe3b6de5746ba04410ee16a1', 1, 3, 'Authentication Token', '[]', 0, '2021-11-29 02:19:35', '2021-11-29 02:19:35', '2022-11-29 09:19:35'),
('42cea1a7175705d8f3374223aed359c961512a4df27360afd25bcdaec174149e422856a776e4f04a', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 04:39:09', '2022-01-10 04:39:09', '2023-01-10 11:39:09'),
('42d43670ac771e5cbf9f14ddad232972fbec09b3f01b97d8176dde86ed3c5fd2b17a65310cc1ac83', 1, 3, 'Authentication Token', '[]', 0, '2021-11-18 06:32:41', '2021-11-18 06:32:41', '2022-11-18 13:32:41'),
('445e6cec4c7e4570ea0f2993f97779640f1806a165cf69dbfd6ca50142459e828fd26b325afa6646', 1, 3, 'Authentication Token', '[]', 0, '2021-11-21 23:48:57', '2021-11-21 23:48:57', '2022-11-22 06:48:57'),
('44b1b826a058f6bdee13921ee8ff00170db64571cc729edf9ce4103942a0bf19aa44b2ed7b8b5d2a', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:04:03', '2021-09-13 23:04:03', '2021-09-15 06:04:02'),
('44d59eb4b7a6ef37637efb1536f7ee13a359da8f11f6801b04044eacc6a942f4185fdf6c6015c8e9', 1, 3, 'Authentication Token', '[]', 0, '2021-10-25 03:02:57', '2021-10-25 03:02:57', '2022-10-25 10:02:57'),
('45f8292d0784ef9dddf1e3f57a8fd4d1693360dcb993e62062fce9863b4d8606f1cd8e07b0909fdb', 1, 3, 'Authentication Token', '[]', 0, '2021-09-29 03:49:28', '2021-09-29 03:49:28', '2022-09-29 10:49:28'),
('46ffb6e160dac6aec561885b68ccc1a110fa63ba1e2ed734db3b730018364ceb8de88bbf0580bb37', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 05:08:35', '2022-01-10 05:08:35', '2023-01-10 12:08:35'),
('47b7f3d05ea0993591f1baa156abc236d40da2e73c7a1091012f572b504498cfac1856c6811c4315', 1, 3, 'Authentication Token', '[]', 0, '2021-10-06 04:52:16', '2021-10-06 04:52:16', '2022-10-06 11:52:16'),
('4839f1408b036b3773fc6e3b4ea2bc4e098deeb472d47a3b50d6b7881ff0b0dfdefe440b27a2c80d', 1, 3, 'Authentication Token', '[]', 0, '2021-10-12 04:55:50', '2021-10-12 04:55:50', '2022-10-12 11:55:50'),
('486458e55acbd153f97ab3f00b226cd24c101a1cc4303c65debb1defd4cfc8b47a161f7c4ee06694', 1, 3, 'Authentication Token', '[]', 0, '2021-12-15 06:48:44', '2021-12-15 06:48:44', '2022-12-15 13:48:44'),
('487d83d8f5b61a1801926ed895ecb53bba8ca803aa7dff6f68ff2b552beff69278b000b93ba97541', 1, 3, 'Authentication Token', '[]', 0, '2021-09-19 20:10:28', '2021-09-19 20:10:28', '2021-09-21 03:10:27'),
('489b6dc2e99d39ea57fecd79c4d703267e6ffd063bf16086a630a97fd48a44e0d56cbaaa88b9ce32', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 04:14:27', '2023-01-14 04:14:27', '2024-01-14 11:14:27'),
('48c5685ce47c7a49c0d560ffa2337ebd5be078edc7fe46b7bb6ed97f9c5a360d0719e32dd5d6af40', 1, 3, 'Authentication Token', '[]', 0, '2021-10-14 15:07:29', '2021-10-14 15:07:29', '2022-10-14 22:07:29'),
('4943dc5ec9afdafcfe615c25574b9a239c834b2310a18319fbdd7390d677a1de9f91e77167cf9b1c', 1, 3, 'Authentication Token', '[]', 0, '2022-01-01 08:50:50', '2022-01-01 08:50:50', '2023-01-01 15:50:50'),
('4960b5f11256dd8290140829dfd051f3b2e9dd53d85b1e559ed590468ff31e57c83a3a9510c14533', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:04:07', '2021-09-13 23:04:07', '2021-09-15 06:04:06'),
('4a5f5808571de3b09babbc675cb3cea289f5a47a3a1762e555f0466ee9b0503c6c1d2689df5cef59', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:06:16', '2021-09-13 23:06:16', '2021-09-15 06:06:15'),
('4b8a769697b703473f617a354b2c8723ddad451779f8a245ee61cc4209ef7993f0c9ae5c4eca38aa', 1, 3, 'Authentication Token', '[]', 0, '2021-12-25 20:00:45', '2021-12-25 20:00:45', '2022-12-26 03:00:45'),
('4b990a15ae7aa07acfd2946ae8657de1589c3cfe0a6b05dab7ab5fd6a416c91f32083d4dbdb87948', 1, 3, 'Authentication Token', '[]', 0, '2021-09-12 19:36:32', '2021-09-12 19:36:32', '2021-09-14 02:36:31'),
('4c9187d7b05a69b75010d0f4c9746e995523d0679d559a95bcc871b4339c3a000e71853704f8b051', 1, 3, 'Authentication Token', '[]', 0, '2021-09-21 20:26:39', '2021-09-21 20:26:39', '2022-09-22 03:26:39'),
('4e55d198279bfbf48a4caa8d2715503075b641dac9dc645a6baf08cdc796c3aa62f1e834a43d44c1', 1, 3, 'Authentication Token', '[]', 0, '2021-10-07 07:34:46', '2021-10-07 07:34:46', '2022-10-07 14:34:46'),
('50bd2c0165614feda497baa4de740dba01c7720ddb19fd37a52a4d4cba0eb67e4fc85bb2760e64f7', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 02:16:37', '2021-12-03 02:16:37', '2022-12-03 09:16:37'),
('51cbf013696b90ea24d5d8f05c31901009306087a9cea7b986a4b463698788581f035f6b635aa02c', 1, 3, 'Authentication Token', '[]', 0, '2021-11-24 03:23:22', '2021-11-24 03:23:22', '2022-11-24 10:23:22'),
('5476c2292e9c7ce81b686964394c1edf20ade2478421b599928360fa6a530ec286bc98a30262b4d6', 1, 3, 'Authentication Token', '[]', 0, '2021-11-29 02:15:41', '2021-11-29 02:15:41', '2022-11-29 09:15:41'),
('55f2bd1b04abb8c5b4d03ee553b3813cbde4d6552c9cb652c118e461050fc59e5a4cbb830a43a117', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 10:13:42', '2023-01-13 10:13:42', '2024-01-13 17:13:42'),
('56d2fb8f0649fd3f7ce45bdc18e9aa81731f037294a8c5d755ace44e8f0278a9460b9891f4a69426', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 09:29:05', '2023-01-13 09:29:05', '2024-01-13 16:29:05'),
('57b35039a41bb411887c8e2972cf50ee6475d90d76284d032a0eb685441c619c5f1451380776af26', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 04:31:26', '2022-01-10 04:31:26', '2023-01-10 11:31:26'),
('5851883924ae496daf71d35a0731483f633d70362a7775490095ce5e7d8afb581e427721d52224ae', 1, 3, 'Authentication Token', '[]', 0, '2021-12-06 01:21:43', '2021-12-06 01:21:43', '2022-12-06 08:21:43'),
('58efecf748ec2be743a0150036e4a6d5303a8667c2fbd39c8532dcc955af5effb156ae5162d6f9b3', 1, 3, 'Authentication Token', '[]', 0, '2021-11-17 07:53:27', '2021-11-17 07:53:27', '2022-11-17 14:53:27'),
('592410387e262ecc8ef2caeea6c44852af211a1c7fa3aff316c2a2edd932d8513f163fcc2e4ff4e8', 1, 3, 'Authentication Token', '[]', 0, '2021-11-07 19:41:31', '2021-11-07 19:41:31', '2022-11-08 02:41:31'),
('595db9e76f007c2d0bbad4b1bad9de7b869867d1084f589a107a812106da9ef5766f4d798af90e8b', 1, 3, 'Authentication Token', '[]', 0, '2021-10-12 06:27:50', '2021-10-12 06:27:50', '2022-10-12 13:27:50'),
('597067c3fba4ae213ee29c5a52f7d13efdd10719e03b8e83385b0e5d8f4d0d2bae21abc832a75501', 1, 3, 'Authentication Token', '[]', 0, '2021-11-20 19:05:16', '2021-11-20 19:05:16', '2022-11-21 02:05:16'),
('5cb1012c4624ada4ad54588de53216687e3b137be351b59451aee4eb0682cd6ff240930dc1c96d61', 1, 3, 'Authentication Token', '[]', 0, '2021-10-12 05:32:11', '2021-10-12 05:32:11', '2022-10-12 12:32:11'),
('5dbb325696bb8bd749780b1d51866319e238c5210b5f14c807acbfe961083620c8c77b64b2735c56', 1, 3, 'Authentication Token', '[]', 0, '2021-12-23 17:18:08', '2021-12-23 17:18:08', '2022-12-24 00:18:08'),
('5efe072d4d59d2a1438bc79833cd5b3430459bd2cc018e41cbd45d5f0a2991632f0f63226917c350', 1, 3, 'Authentication Token', '[]', 0, '2022-01-07 06:12:06', '2022-01-07 06:12:06', '2023-01-07 13:12:06'),
('5f3a4568cff6c4827f1e414e741136bae9408d9dbeaf1ee4ddf37f865dd2ea59128bfdb905daa825', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 09:59:23', '2023-01-13 09:59:23', '2024-01-13 16:59:23'),
('5fdcf4a22a3cc10c064539ab92f3af9ffbf41a1378e223f6c6fa1b4bdae32c0a205e8154134fe3da', 1, 3, 'Authentication Token', '[]', 0, '2021-09-12 20:06:48', '2021-09-12 20:06:48', '2021-09-14 03:06:47'),
('60dd54ff64ddf16090497776ebd657f1b74c390449cfb0658063b89413558d57a70c002aac2992d7', 1, 3, 'Authentication Token', '[]', 0, '2021-09-29 08:40:29', '2021-09-29 08:40:29', '2022-09-29 15:40:29'),
('625c6b0f193530de14759e51f8a05b61da53c83da10a103075d27c1f90c125825137545f7b3fe1d6', 1, 3, 'Authentication Token', '[]', 0, '2021-09-29 02:45:39', '2021-09-29 02:45:39', '2022-09-29 09:45:39'),
('629b2fad8990a086c9b59fac374663d778e54f39dac74e1b0f68039155e6fdab909a0aaab13c7efe', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 01:13:56', '2021-12-03 01:13:56', '2022-12-03 08:13:56'),
('63c70dc1606700b2b9663501050626e41832518b801da3627b9a0590910439c7f3b34fdf17e27bf0', 1, 3, 'Authentication Token', '[]', 0, '2021-07-31 12:56:59', '2021-07-31 12:56:59', '2022-07-31 19:56:59'),
('648601ba80727310c5c90abedc5fce8a3496a1e3a067e88158dc905f3b9bd03d5d337f48a4630035', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 07:35:42', '2023-01-13 07:35:42', '2024-01-13 14:35:42'),
('64f2e8aaa1aa5bf51e8f8cbac8c2894d80369d53381ab1aee66163d753bb9c4ebb1cf7610cb99a8d', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 09:28:34', '2021-12-02 09:28:34', '2022-12-02 16:28:34'),
('660170f61b042fa8bf0d6b279beee2beee25c69fe09638117f513172c1603f50f856594520a75888', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 04:11:01', '2021-11-25 04:11:01', '2022-11-25 11:11:01'),
('66029e33d02d5633527ea1c3fc49d4584dbecbe791b7950ed19f624924317d285a497084643d6cd2', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 01:41:34', '2022-01-10 01:41:34', '2023-01-10 08:41:34'),
('6678aa23837dfb0280aa6e6cce9e5169a2cdc3b444c616419af8899607adf101fb091f4699a4f3fd', 1, 3, 'Authentication Token', '[]', 0, '2021-09-21 22:11:09', '2021-09-21 22:11:09', '2022-09-22 05:11:09'),
('67157b3b98e855d5b2b37d8bd15ffc785df5a3e86edee36190efd4d2a37c856f2c6917786f42ad1f', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:53', '2021-12-02 07:46:53', '2022-12-02 14:46:53'),
('677a0be2d474aa83dcd04e611ed4c6b1c8aea2d583cede844282128d67a3105b73bfce6b9aaae37b', 1, 3, 'Authentication Token', '[]', 0, '2022-01-07 06:07:13', '2022-01-07 06:07:13', '2023-01-07 13:07:13'),
('67ca16ed345370f671fa50ce3a325978af59016c0819b1d77cdb1c967610ac77c0071a3ffe8e0db5', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:37:56', '2021-09-22 04:37:56', '2022-09-22 11:37:56'),
('68702f9a6f554a8eefd9a5dd137d6bb7a743dd9f6d6895902e20edb9e917580f0ce35e58f90dd7c2', 1, 3, 'Authentication Token', '[]', 0, '2021-11-17 06:25:38', '2021-11-17 06:25:38', '2022-11-17 13:25:38'),
('6937d59fcaa6f57d6807aacb6bf90b34d0b7c92d4d3d41657f9abad081312d60bf7b38b2e1b9ac56', 1, 3, 'Authentication Token', '[]', 0, '2021-09-30 05:50:06', '2021-09-30 05:50:06', '2022-09-30 12:50:06'),
('6a0f61069314af5495e301576db51b1ddf5cf15dc1b980ca514f734f6fe6572ff0d2d47fcaa53f14', 1, 3, 'Authentication Token', '[]', 0, '2021-08-19 01:37:47', '2021-08-19 01:37:47', '2022-08-19 08:37:47'),
('6bc7533ca9f89b4e0d40cba898fbe531ead3b92970274d4e88297c6b1f7e97edc348f322ca447a1f', 1, 3, 'Authentication Token', '[]', 0, '2021-08-08 19:43:15', '2021-08-08 19:43:15', '2022-08-09 02:43:15'),
('6c851d71609df5b8c6823175d9f2943e43908b2febe5d8c8be017dc42ef5001da024cb4544e59afb', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 03:47:41', '2021-11-25 03:47:41', '2022-11-25 10:47:41'),
('6ccb7f7eab4fc5e077bd3fe87bbc515966b60b00ca642c8e6aa36db5484c6028dbaae0c64cfd681c', 1, 3, 'Authentication Token', '[]', 0, '2021-08-19 08:05:46', '2021-08-19 08:05:46', '2022-08-19 15:05:46'),
('6d326500a93fe2a4e4ec2449bb79cef433b0a2e865d349eadea1ef9b2a3b651d4dea58d2717bad99', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 18:55:29', '2021-09-13 18:55:29', '2021-09-15 01:55:28'),
('6dc1499123faa101dd2197c2f99335e57ac8eba028cf7c5ada06ddea82846f683db2bd4fcf7486af', 1, 3, 'Authentication Token', '[]', 0, '2021-11-08 02:16:46', '2021-11-08 02:16:46', '2022-11-08 09:16:46'),
('6e34c227997d23005244087a7e04e7f053f28160c156a7db73e005ff398ee58360e6e0a8bee191d8', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 07:36:49', '2023-01-13 07:36:49', '2024-01-13 14:36:49'),
('6ead7be3be66029c47429285e140b75fb0aada039f245bcc3176922ffaac4d8afedff73abbe2c80f', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 06:29:15', '2021-09-22 06:29:15', '2022-09-22 13:29:15'),
('72324ccb38df097de7bf50378c6018a6ed2f28472e70e0eb2f40b097d2e7299fc3d57e9c7c362d54', 1, 3, 'Authentication Token', '[]', 0, '2021-11-18 03:12:41', '2021-11-18 03:12:41', '2022-11-18 10:12:41'),
('72e2fea4e7e538ac03ab237208b9b14c4f313a1d5788f462f92aec188e21a35ac2fbe20d531ce771', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 00:39:01', '2021-12-03 00:39:01', '2022-12-03 07:39:01'),
('7418a426a40ca27209ad8a0a9ab97533e73b1bf2e85edbcb243beb9f40fa5ba4231c6d511c16232c', 1, 3, 'Authentication Token', '[]', 0, '2021-07-31 09:30:49', '2021-07-31 09:30:49', '2022-07-31 16:30:49'),
('76388b5ac1f2b29bc43de1f3b1bbc3733d2c327c617e58e3c47699200a159f55d44a8fccce8c9434', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 10:37:09', '2023-01-13 10:37:09', '2024-01-13 17:37:09'),
('763f436f7f4d566279ed7540910e40a9d5e5eefe2d2754527c48eac1c6a17d64706ac7d72087e856', 1, 3, 'Authentication Token', '[]', 0, '2021-10-04 10:49:42', '2021-10-04 10:49:42', '2022-10-04 17:49:42'),
('765b042768ca870af4894f0eb42927a39f7292361054b2314c746bb69b407c015dfe42c021fde01e', 1, 3, 'Authentication Token', '[]', 0, '2021-12-15 06:31:51', '2021-12-15 06:31:51', '2022-12-15 13:31:51'),
('76bc67ef575724d2b98c8c13a3a1da6814096444fb0ff8a6dd15dffeb96d8edbff873ebe7e0e79dc', 1, 3, 'Authentication Token', '[]', 0, '2021-10-10 17:13:37', '2021-10-10 17:13:37', '2022-10-11 00:13:37'),
('76d0d6db591f6775d1dbaa9120945868c649f32a6da12de37da3b037e45037508561f61fc4188ba1', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 03:38:30', '2023-01-14 03:38:30', '2024-01-14 10:38:30'),
('7736a1972232dcf8bae829aae1622e94df924c815606330fb379c6eb267824de140a496109ee81b9', 1, 3, 'Authentication Token', '[]', 0, '2021-10-01 02:46:52', '2021-10-01 02:46:52', '2022-10-01 09:46:52'),
('78355d5c0e0435186e3d7c4d51cc2d13715106aa412b6d223c8f3d03906a218266db96941cbcd40b', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 19:50:59', '2021-09-13 19:50:59', '2021-09-15 02:50:59'),
('79543362f3b463a4ff4f5bd96f3931c40af1ccbe8c6384d4082261435bb6db0e60538e8cf3265ef7', 1, 3, 'Authentication Token', '[]', 0, '2021-12-13 07:41:49', '2021-12-13 07:41:49', '2022-12-13 14:41:49'),
('79de6aeefce8ba87044563a06c9d7c54951fd23c3b4f6f8d78693ff6b47696d4c8a1ca68bfe432e4', 1, 3, 'Authentication Token', '[]', 0, '2021-12-23 15:33:56', '2021-12-23 15:33:56', '2022-12-23 22:33:56'),
('7bb6e8d891902136073cf1fcd714d94f283934d3043760afb12caad41356e4f335e0f2f2adaace37', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 19:49:51', '2021-09-13 19:49:51', '2021-09-15 02:49:50'),
('7c91e8242a5f04e598f6925caf9c3e57cc6a9863766448710e2ace580f0db6e34ae5134527c0028e', 1, 3, 'Authentication Token', '[]', 0, '2021-10-27 07:36:33', '2021-10-27 07:36:33', '2022-10-27 14:36:33'),
('7ea452439464edd00ea6f90e71209b94755b547be8c0c94afa8fb09984ce5e99df25ae9cff21fe68', 1, 3, 'Authentication Token', '[]', 0, '2021-11-17 06:34:48', '2021-11-17 06:34:48', '2022-11-17 13:34:48'),
('821e89a0d7d737c35f701fc086fdcf5feedac3bb7c813a6392f5947effc5766c48f9a08829d4feaf', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 03:45:36', '2021-08-04 03:45:36', '2022-08-04 10:45:36'),
('832a166fedb1adba0b4ad2a144fce56a73e6a0380ff1d8c6ff0ac68938b4c4330b0379750293295d', 1, 3, 'Authentication Token', '[]', 0, '2021-12-25 20:41:18', '2021-12-25 20:41:18', '2022-12-26 03:41:18'),
('841b698a377f7f06c150702cc245255e9b4b11d4cae7d03021207dfc5fa8fe3591987b7cf2a8d16c', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 19:37:18', '2021-08-04 19:37:18', '2022-08-05 02:37:18'),
('841db61bf27848a42e826e512e7af148c77cfdcfbe575a2a4fefe82062f644d2815753a90144e401', 1, 3, 'Authentication Token', '[]', 0, '2021-12-16 01:57:02', '2021-12-16 01:57:02', '2022-12-16 08:57:02'),
('862f29c5fff0fa6261278dc3585a2d2b27e0644649d7acf9779f9a583f413005ee370d6663d6fb2a', 1, 3, 'Authentication Token', '[]', 0, '2021-11-03 03:20:43', '2021-11-03 03:20:43', '2022-11-03 10:20:43'),
('88f4ce9fe53723c58caaf96fd711fbf6d60da859ed250f33a8a4564088fe070797a57cca7b3ec252', 1, 3, 'Authentication Token', '[]', 0, '2021-10-19 03:13:09', '2021-10-19 03:13:09', '2022-10-19 10:13:09'),
('89a2cd900033bb6c2cb248973a55a1c52c5f2a7fff173c655406c70019178e1598871e7335784950', 1, 3, 'Authentication Token', '[]', 0, '2022-01-07 06:07:13', '2022-01-07 06:07:13', '2023-01-07 13:07:13'),
('89b1958ad117fd71cf4ea4e1ef031cfe226c094d3b8acb5a005f20b0ce35a044f380191f3c96baef', 1, 3, 'Authentication Token', '[]', 0, '2022-01-02 10:19:27', '2022-01-02 10:19:27', '2023-01-02 17:19:27'),
('89f6316d1048951761e2d00f742c430387a165ce3f032a84e00aea5fded7eb42e8d71ae8d0899de3', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 04:23:20', '2021-11-25 04:23:20', '2022-11-25 11:23:20'),
('8a923ece1d89c34ec2105fa19fd3fc4fad7e316742a774f340a88adb5c64eef36dd009f1769bc210', 1, 3, 'Authentication Token', '[]', 0, '2021-11-24 06:21:24', '2021-11-24 06:21:24', '2022-11-24 13:21:24'),
('8acbb1231a6cbd1eca07fa03a2cc62c896cc5c19fa70b6b6db833ba1d49fe1a6f1e01dd47b509d9b', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 11:40:56', '2021-08-04 11:40:56', '2022-08-04 18:40:56'),
('8bf8a7bf0f3883ee311ce96befcb6e8dde729a35e1dddcf7d1f0d9b42c11fb5610f365d6eb04b482', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 03:35:47', '2023-01-14 03:35:47', '2024-01-14 10:35:47'),
('8c4763217efa2860d65a217a05b692887a6fbfa8870a3cbc346f061a9a3913c27798b94c9e9c6f6a', 1, 3, 'Authentication Token', '[]', 0, '2021-10-08 02:31:51', '2021-10-08 02:31:51', '2022-10-08 09:31:51'),
('8da27bdfa179f8a5c3922569708e412b494c452a40ce49804ac9f720b6f4f89e5270807fd5516581', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 23:54:46', '2021-09-22 23:54:46', '2022-09-23 06:54:46'),
('8ed8e0a7c4b82268b9c7ff8dd6113774cb65996093096fbc61fe8c85bf9c04ed21588f19126a6534', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 04:16:27', '2022-01-10 04:16:27', '2023-01-10 11:16:27'),
('8f0a43e21daf84696467566f0e49913760739817bb5d32faf0b90b4b00cd772f5dc42b0dc2591a12', 1, 3, 'Authentication Token', '[]', 0, '2022-01-04 05:10:40', '2022-01-04 05:10:40', '2023-01-04 12:10:40'),
('8f8ed55be6b53283b12683c8846f27e86528e1f3ffa6c73005da58a612c05e2b071360f3798eb468', 1, 3, 'Authentication Token', '[]', 0, '2021-08-19 01:37:49', '2021-08-19 01:37:49', '2022-08-19 08:37:49'),
('905e8d4133e85c161bea0bba3423036a2b2fc72f0631f99810b617cf79d2bb4574bd6d1a14fe39f0', 1, 3, 'Authentication Token', '[]', 0, '2021-10-10 17:11:04', '2021-10-10 17:11:04', '2022-10-11 00:11:04'),
('9331d4b163948616500c8b709d123543bfb3d121123df9a354e65a7ffd2e8a1ef52381e381422ddb', 1, 3, 'Authentication Token', '[]', 0, '2021-10-12 06:40:07', '2021-10-12 06:40:07', '2022-10-12 13:40:07'),
('95e64e2bf783f5af064ee0c72b01e40d676ed5bd3377891786bb4944334781aa2c5d3afd60b96253', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 19:37:05', '2021-08-04 19:37:05', '2022-08-05 02:37:05'),
('96d34e6b71d76e71189637c55a5a605a3f01b9b62b8c3d1f4dc3cdbebea9f8b489dcf34b1f271b19', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:43:36', '2021-12-02 07:43:36', '2022-12-02 14:43:36'),
('9ac8d5d6c7863f931fab388a45e4d12efae7847eb73d1f472c55e3e4e4c26e63c88f5bfc66bd2739', 1, 3, 'Authentication Token', '[]', 0, '2021-10-12 07:51:36', '2021-10-12 07:51:36', '2022-10-12 14:51:36'),
('9b6d5f1ba30155c1aeb3dccc9914002dbf91faa46a4dbd6e2d49f8ab768dcc851a15476600fdf5a8', 1, 3, 'Authentication Token', '[]', 0, '2021-08-03 06:41:13', '2021-08-03 06:41:13', '2022-08-03 13:41:13'),
('9c084cdf3b4107bfa0e7e02f4089c3fbde0cd1659de0eb362d392b01c5a19512a1835b3c51928cdd', 1, 3, 'Authentication Token', '[]', 0, '2021-12-06 01:22:35', '2021-12-06 01:22:35', '2022-12-06 08:22:35'),
('9c17d7f8cd88555a87f13c1d7df104eec1e5b1069f6fa1bea5083c0c347469f010726fa5147c35ea', 1, 3, 'Authentication Token', '[]', 0, '2022-01-03 21:22:05', '2022-01-03 21:22:05', '2023-01-04 04:22:05'),
('9c88404ca652238b4465a7e273a9cdf2fc41d3c1c8a803247ac1d069bb3678a78d0c21eae541747e', 1, 3, 'Authentication Token', '[]', 0, '2021-12-13 11:23:21', '2021-12-13 11:23:21', '2022-12-13 18:23:21'),
('9dcf45a515440ef0530c5b91b0a2d395b3e7695e3fb172a5cd200412f63df6deea55a1c8c363806f', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 03:41:59', '2021-08-04 03:41:59', '2022-08-04 10:41:59'),
('9e821be39fbccc87b943e4917160eb2d04c8207d548d0132831b302a103f6631db2f0b418b6d2f25', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 03:41:04', '2021-08-04 03:41:04', '2022-08-04 10:41:04'),
('a1db18c010878c02cdfdfb577aa1a515f022f64091f580c021a05c910bb4ea7fdb719a6cd7dd3357', 1, 3, 'Authentication Token', '[]', 0, '2021-10-29 04:40:47', '2021-10-29 04:40:47', '2022-10-29 11:40:47'),
('a257979fd6404449caed6bd957915cbd85981ba5f4bdcc164bb308282779969456defdc4bc11a3a8', 1, 3, 'Authentication Token', '[]', 0, '2021-10-07 07:32:06', '2021-10-07 07:32:06', '2022-10-07 14:32:06'),
('a46bf537e1276058e5aa20053edd99fd7a9e43f30024671eb31e732477da389e4dbda01f75495cff', 1, 3, 'Authentication Token', '[]', 0, '2021-12-13 10:55:09', '2021-12-13 10:55:09', '2022-12-13 17:55:09'),
('a48b5fbfc1838f3e2b1a25b17f43e09a6af9c269c562084bf09cf99d2f59c17b3d78be1d66a3d39e', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 03:45:01', '2021-08-04 03:45:01', '2022-08-04 10:45:01'),
('a4ecc3707be648aa1cfa25fb9b6a0e271e0438bcc648b71993fb496f8d4df48b15b6a7fdc1810166', 1, 3, 'Authentication Token', '[]', 0, '2022-01-04 05:02:05', '2022-01-04 05:02:05', '2023-01-04 12:02:05'),
('a50956828377628401dcf66b507dbc55b4b5f5ac3592437fa81146b0268d67edb101060899710571', 1, 3, 'Authentication Token', '[]', 0, '2021-08-29 19:27:02', '2021-08-29 19:27:02', '2022-08-30 02:27:02'),
('a509d03d384e2a7218ca7b2416af170b7c0aff11a587ee2cc7d8222cd8e0eb530d17b1ed84124a63', 1, 3, 'Authentication Token', '[]', 0, '2021-12-31 10:12:22', '2021-12-31 10:12:22', '2022-12-31 17:12:22'),
('a62467ece15b68baa2828486a45941cc7718aa17a8d4fea9d5a854b4f3a51e5ab3ee10d08f057c75', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 04:57:59', '2021-11-25 04:57:59', '2022-11-25 11:57:59'),
('a6ee3f06af31509e04ef083daf0b35ba7e9024c525d1c89a403bbf6f3005bcaed0a3aa5f26d8b85e', 1, 3, 'Authentication Token', '[]', 0, '2021-09-14 21:01:54', '2021-09-14 21:01:54', '2021-09-16 04:01:54'),
('a79c119d6c726003d90201eb34f6b1b2db2f5d2e00886019bd7aa833595c368513b242feb6d4e4f0', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 13:31:42', '2021-08-04 13:31:42', '2022-08-04 20:31:42'),
('a8007e6e64c731cf6c019024b2425d5398935c7e4157ed23d0158a593abd84de91e6c6eb7e1cc3e0', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:53', '2021-12-02 07:46:53', '2022-12-02 14:46:53'),
('a83ad8f892073eed2b5ce7c9b4627c5d540ef3ab25985815abd74da3415573460a63dca4c1de0a70', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 19:46:06', '2021-09-13 19:46:06', '2021-09-15 02:46:06'),
('a897ee29bebd9e0ecb2ae47db1ff2140b378f3a7fd3015d595a4fad7647adee2381533ef3ef64c98', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:04:08', '2021-09-13 23:04:08', '2021-09-15 06:04:07'),
('a8d18689e87a72dc7df74719fef5eb6965c7790f4884a41b397f6dcb2cbb930d15acab7f2f07a564', 1, 3, 'Authentication Token', '[]', 0, '2022-01-01 08:42:39', '2022-01-01 08:42:39', '2023-01-01 15:42:39'),
('a9498a1c3224030990fb6222605b81ac79d4379f7a4983555600866b53e4f2259e19f4db6de23ac7', 1, 3, 'Authentication Token', '[]', 0, '2021-10-06 02:59:40', '2021-10-06 02:59:40', '2022-10-06 09:59:40'),
('ab12af5e53cf93caaf9e83d24a7cee37f9814ca14dcf4bc9d94a22b987d9aef175473dcc45b6cc65', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 05:05:23', '2021-12-03 05:05:23', '2022-12-03 12:05:23'),
('abac01318d8530e344de93661347b09a9579123507b72c7da140cb6eed4a2eb00feee9043faae31c', 1, 3, 'Authentication Token', '[]', 0, '2021-12-07 00:58:23', '2021-12-07 00:58:23', '2022-12-07 07:58:23'),
('abfb465dd981b19b227890de80898e889c926a11fea526696e6a32278242bc8ca2db9225d66a74e0', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 03:44:15', '2021-08-04 03:44:15', '2022-08-04 10:44:15'),
('ace47e30c5ce75378b1bd0cba79d2c2b0c26df8eda824afb29cbcbd4412c8e7aaa0c8eb1912b26a5', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:04:09', '2021-09-13 23:04:09', '2021-09-15 06:04:08'),
('ad1fb6b845c6bb865ad343eb557878e1a882da151e6d5f64facd766d87a89433d0909532e6cc2cf6', 1, 3, 'Authentication Token', '[]', 0, '2021-11-17 03:22:02', '2021-11-17 03:22:02', '2022-11-17 10:22:02'),
('ada25c9906cf16d357e24a898336b744c00e6b555830fdd3d1cf1605d555a176e6c4d36182a970dc', 1, 3, 'Authentication Token', '[]', 0, '2022-01-09 09:22:31', '2022-01-09 09:22:31', '2023-01-09 16:22:31'),
('ae7a9f5dba31d15c9f6d500228bfd0fd57382ce072f708ad8afab6c2541c430cf80813dd8dd15576', 1, 3, 'Authentication Token', '[]', 0, '2021-12-05 12:42:26', '2021-12-05 12:42:26', '2022-12-05 19:42:26'),
('b3b9c89af6fef25f053affabc12a5e86803fa56ad4ed9e5746efd8aed491b527966524d303a44d52', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:53', '2021-12-02 07:46:53', '2022-12-02 14:46:53'),
('b411b93ccbb96a4bacb92508d445a8ea07e34dfa5736aa4aaaca4c5212ef4d192e6c513820b22b9e', 1, 3, 'Authentication Token', '[]', 0, '2021-09-29 03:30:07', '2021-09-29 03:30:07', '2022-09-29 10:30:07'),
('b56a994eb01e2bb3daf83eea245c6ef126d25f451193a8e0a9f8b9d721c83c1e1682d7b8fdc59720', 1, 3, 'Authentication Token', '[]', 0, '2021-11-02 18:33:25', '2021-11-02 18:33:25', '2022-11-03 01:33:25'),
('b6cb9fb9f8b1b090abd2e2f53f482b04c1cb85ea7dee6db13462056f8d8c15d91483b974859638ec', 1, 3, 'Authentication Token', '[]', 0, '2021-12-30 09:10:06', '2021-12-30 09:10:06', '2022-12-30 16:10:06'),
('b6fcbac82c9d10f33222bcb591b308049120edd2b0a6de98a8d4dec6126eb53dbc5c965565e9592a', 1, 3, 'Authentication Token', '[]', 0, '2021-08-19 08:06:14', '2021-08-19 08:06:14', '2022-08-19 15:06:14'),
('b813836eca8d74308132f1bd0e60b2277853b27cd5a284ddf6bd5f6a5a0f9e40b94b816d6f6e4729', 1, 3, 'Authentication Token', '[]', 0, '2021-09-06 20:44:08', '2021-09-06 20:44:08', '2022-09-07 03:44:08'),
('b86c1d0e135c9050ec0724023f7287cac024846530ed3dee738d5f229892383dec8a4e32ad481c94', 1, 3, 'Authentication Token', '[]', 0, '2021-08-22 13:17:34', '2021-08-22 13:17:34', '2022-08-22 20:17:34'),
('ba126bccac32e9bd4c437f25c3e4bdc9c0bcf3220826b1e5bf68d1b86e05c94b91e192731928fdfe', 1, 3, 'Authentication Token', '[]', 0, '2021-10-29 03:52:41', '2021-10-29 03:52:41', '2022-10-29 10:52:41'),
('bcabcddd51442e75acbd08a15e0dae21eaf15f538494c8d5ce76bf9d228e79f7a7ddee40f1dc55bf', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 19:28:53', '2021-08-04 19:28:53', '2022-08-05 02:28:53'),
('bf228b04e227d371d626872cdd4a9fdd8b900f566d61b6f4fe1ee8dcd1f10cf876e38c76c249aef2', 1, 3, 'Authentication Token', '[]', 0, '2021-12-31 09:56:35', '2021-12-31 09:56:35', '2022-12-31 16:56:35'),
('c2ad227f61f2f8acf53f3c4f7462bf803cb726fa5bde2a49cac09a780b636ad94240bb01c8182375', 1, 3, 'Authentication Token', '[]', 0, '2021-08-13 00:06:30', '2021-08-13 00:06:30', '2022-08-13 07:06:30'),
('c353094d14f07dd7b678bfd7d99d45c1c76760cef6a951917c5c76737c2e3cf5ef72ceab39efa36b', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 06:32:55', '2021-09-22 06:32:55', '2022-09-22 13:32:55'),
('c42b665208374d12de8e5905b5e223d54fba00a31d80df24184cbde74242f35a5ce9ffa404bd4e51', 1, 3, 'Authentication Token', '[]', 0, '2021-12-05 12:42:27', '2021-12-05 12:42:27', '2022-12-05 19:42:27'),
('c44a4b39c339040e9e1a30a3e6b5100c2d51a75f1c6bde94bb27b5c94ca54e2590225f5e3d78ba83', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:04:11', '2021-09-13 23:04:11', '2021-09-15 06:04:10'),
('c660671a5326904bd35f66a07282bf3589d7897727a4d709d9bfddf7b5fde9c3265d6f58e8b4799b', 1, 3, 'Authentication Token', '[]', 0, '2022-01-10 04:20:23', '2022-01-10 04:20:23', '2023-01-10 11:20:23'),
('c7568b6e815af93ad147e6f75797901edd1b518fc7566a3f0423c870a2d0afcf9b2c1bd3cd508bdc', 1, 3, 'Authentication Token', '[]', 0, '2021-10-06 02:22:28', '2021-10-06 02:22:28', '2022-10-06 09:22:28'),
('c7a31a7c96165524a226f1fc893b4f2f093d8d413dcbd5660d66388b790b897eac50410655658e8f', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 04:21:21', '2021-09-22 04:21:21', '2022-09-22 11:21:21'),
('c7e560438949fc1274d2e5974ecefa2d4312a349c9bc6cdb1c2fbe89e9cc42adba7278500114ad81', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 06:57:58', '2021-08-04 06:57:58', '2022-08-04 13:57:58'),
('c8ad3eeeb155cab2f3d6c8e6cece536f090b066b696d2016b5f446147972b3ce866bc269cbecd6a4', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:52', '2021-12-02 07:46:52', '2022-12-02 14:46:52'),
('c9e379ed79767b0f52e8b7b934036c0eb02d0bd15b5c9868785b6646c396c355fe25cebe70d5fcd6', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 01:09:26', '2021-12-03 01:09:26', '2022-12-03 08:09:26'),
('cafa43318e88a4a088646f5813ce2ab50df25adc100c1e88368ac83295f2b80be58ab5ac010eecbe', 1, 3, 'Authentication Token', '[]', 0, '2021-10-29 04:40:45', '2021-10-29 04:40:45', '2022-10-29 11:40:45'),
('cb30f487bbafb0ab55bb904cccb3cd712e4ac5476cf3f1d0a93f83e48fb8e013572bb1069c665c2f', 1, 3, 'Authentication Token', '[]', 0, '2021-10-29 04:57:01', '2021-10-29 04:57:01', '2022-10-29 11:57:01'),
('cc0f1c88b67da8311ac03304efaeec9ac61bdeadf0fc780bf891587d1ffba7b7fca81e05d4f27157', 1, 3, 'Authentication Token', '[]', 0, '2021-12-14 01:46:49', '2021-12-14 01:46:49', '2022-12-14 08:46:49'),
('ccc0161f288bde8aadfe55a07bd025bee320b3b93a4aeddfa89df97d16f18944eecf5b938fa64f38', 1, 3, 'Authentication Token', '[]', 0, '2021-12-14 02:52:34', '2021-12-14 02:52:34', '2022-12-14 09:52:34'),
('ccf86be88b26b556aee11722c2995b588161d567fd818072e96d51d3de8acc04f6d0d47e5ed2020c', 1, 3, 'Authentication Token', '[]', 0, '2021-11-24 06:21:25', '2021-11-24 06:21:25', '2022-11-24 13:21:25'),
('ce4bdd61efd37a7faa5a60e60f5e5c9b7e5160967406ff350e2a37d9b57dbe0516c635c19fe38fef', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 06:40:48', '2021-09-22 06:40:48', '2022-09-22 13:40:48'),
('d1da9ddd20b6fd5aa3dd88cc40113e68fa64f742f6c33c92f58549604af29efc1e4786f6c99027b2', 1, 3, 'Authentication Token', '[]', 0, '2022-01-07 06:54:12', '2022-01-07 06:54:12', '2023-01-07 13:54:12'),
('d1e22f1f29d006a53cf556f41b6d46ef3468b931a74f0a1e8b1fefd991bfd5b4d02f96fdce4aaf7f', 1, 3, 'Authentication Token', '[]', 0, '2021-10-08 06:27:00', '2021-10-08 06:27:00', '2022-10-08 13:27:00'),
('d1fc0beb9b67bfc29ab9bdb7230bc49e210cab030be59349a0c3b6393b6ca90f70bde6079d1ba7d6', 1, 3, 'Authentication Token', '[]', 0, '2021-09-19 20:46:26', '2021-09-19 20:46:26', '2021-09-21 03:46:26'),
('d22ae7c2a4f1cd551a80479ce256e222e94eba88fc732509db430e76c4c9be60986c8cb84cca9ee3', 1, 3, 'Authentication Token', '[]', 0, '2021-10-11 07:38:16', '2021-10-11 07:38:16', '2022-10-11 14:38:16'),
('d25462cd7f9afd685d755ca940a10eb776e778a7cc3f39dc727e058877b3ab9456213c92eea28f35', 1, 3, 'Authentication Token', '[]', 0, '2021-12-22 01:00:50', '2021-12-22 01:00:50', '2022-12-22 08:00:50'),
('d25b49335a7ca4aa46abbbe42b4f50cbd55848bbaedbb61de2465ceee6a2ac7173bf6ac341498d83', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 05:31:45', '2021-09-22 05:31:45', '2022-09-22 12:31:45'),
('d27e2fdd3a2549b213af319ac35775aa45e4b7ca8164d4031249e0d2c521e6351f9c9a0b04de51c2', 1, 3, 'Authentication Token', '[]', 0, '2021-09-12 20:07:26', '2021-09-12 20:07:26', '2021-09-14 03:07:25'),
('d48688fba713f48eda3a4be71a578e68d13d31d202f1ff6e04509506c1164b4b5f5268dec7ae1f16', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 04:18:05', '2023-01-14 04:18:05', '2024-01-14 11:18:05'),
('d6a77b8e14c1dbc09002a3e7026b3faa0d3baff95d44d5040379cae22c853995e9387238f6f4990a', 1, 3, 'Authentication Token', '[]', 0, '2021-12-25 20:00:47', '2021-12-25 20:00:47', '2022-12-26 03:00:47'),
('d743ed815a71ec8f45d2245c1941f41abf21cab43c5fdf4b34187d2218183823e9a7d830bb050315', 1, 3, 'Authentication Token', '[]', 0, '2021-11-24 04:14:36', '2021-11-24 04:14:36', '2022-11-24 11:14:36'),
('d89afb54491c23eb41253835b284346915371e69b8247ce4758c171674c863654395dfc1be201052', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 03:28:43', '2023-01-14 03:28:43', '2024-01-14 10:28:43'),
('da05359f1f7546641a631c705e2ba366ece6eadd23cbefb34383fa89e8e3e240f710e93d5ae275b5', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 09:34:46', '2023-01-13 09:34:46', '2024-01-13 16:34:46'),
('da86559a5bf332be7a6070ee1f752f299b1ffbf0e673045e2bd2163483483b1af90e7eef5aacd90d', 1, 3, 'Authentication Token', '[]', 0, '2021-11-29 03:41:26', '2021-11-29 03:41:26', '2022-11-29 10:41:26'),
('dc65211cd8f1ac926e2cdf82270a2012500692e97c5a218bb0fce340b2bdbf6ebc2c29fa65573f0a', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 23:04:04', '2021-09-13 23:04:04', '2021-09-15 06:04:03'),
('dd563bd077844bd43af41403f1faae71ce0bc4cbfed9f49d87629fd1c02b89dfb295c3be4fe77be2', 1, 3, 'Authentication Token', '[]', 0, '2021-12-16 01:57:01', '2021-12-16 01:57:01', '2022-12-16 08:57:01'),
('e4213c48c143873112a54a3bfffe3ea848b94a5b497e657552f0dc6d5e340cdaefa28b6b095e8c0d', 1, 3, 'Authentication Token', '[]', 0, '2021-12-15 00:55:08', '2021-12-15 00:55:08', '2022-12-15 07:55:08'),
('e47b5058f9c3e2798a51a557f4f48bb94aedac6e543a6d4c0c656af78fcc36de6d8e83380a10bc64', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 00:43:55', '2021-12-03 00:43:55', '2022-12-03 07:43:55'),
('e785c5bfb8fecf1ca8ecfd690038336e7867a6fd1fd088617dec2ec5327f4376b93087a23681582f', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:52', '2021-12-02 07:46:52', '2022-12-02 14:46:52'),
('e7bb5fd85590e31a32eb1690ea21a496e4a7f5de875bcff4bd0651fc718d8c00aecb40d47a0a109c', 1, 3, 'Authentication Token', '[]', 0, '2022-01-05 01:23:26', '2022-01-05 01:23:26', '2023-01-05 08:23:26'),
('e8ef3e3f774d2edb859bd261d2698eebaf65edb379b6508440ad3c3222d3bb2ffeb1d8e58922fc34', 1, 3, 'Authentication Token', '[]', 0, '2021-12-13 11:23:20', '2021-12-13 11:23:20', '2022-12-13 18:23:20'),
('e99a325d9dfa4c878fbc9053f7f00e00e644d9669de6c8b8afcf23a32e5a0c9b16c6516fa36d32e0', 1, 3, 'Authentication Token', '[]', 0, '2021-09-21 22:11:37', '2021-09-21 22:11:37', '2022-09-22 05:11:37'),
('ea21120c9954eef67d1780a2c0a57e3e9b0280599a2ff20c7c8b53fc9e38025a6a4d693c4046e8e2', 1, 3, 'Authentication Token', '[]', 0, '2021-11-17 06:13:42', '2021-11-17 06:13:42', '2022-11-17 13:13:42'),
('eacd2d1519f23c4cad8c3c5c631efb1f182ec42148bb60277b6519eed398f90a200332a21b72747c', 1, 3, 'Authentication Token', '[]', 0, '2021-11-25 04:14:54', '2021-11-25 04:14:54', '2022-11-25 11:14:54'),
('ebd169486532083b99898cebb67533cef52de36b896a96e6283892f155fff823988766edaa8161d3', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 11:46:45', '2023-01-13 11:46:45', '2024-01-13 18:46:45'),
('ebe239e2596e3cbe42d181604825389df1fc97fea4dfc8e2e10ddb79168e52310095283206a0d026', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 06:31:41', '2021-09-22 06:31:41', '2022-09-22 13:31:41'),
('ec10b00dfb9bbec8e8ad1e2e1f20893cc5c81d2c30435f183299fa21b87e563171818607826e5d80', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 03:34:17', '2023-01-14 03:34:17', '2024-01-14 10:34:17'),
('ec3fbe2fc619828c3866ae9b830801b53bbbb0aa62e9965e4ee4b5f92d0f4fa83bf37f9d48cdbe47', 1, 3, 'Authentication Token', '[]', 0, '2021-12-02 07:46:54', '2021-12-02 07:46:54', '2022-12-02 14:46:54'),
('ec84879c4b811f57135a549761b0cbdc2a36bee11ac107a61fa2494dd1769b22be9d6beef8662bc0', 1, 3, 'Authentication Token', '[]', 0, '2022-01-03 21:31:47', '2022-01-03 21:31:47', '2023-01-04 04:31:47'),
('edc15f908ab7901fbcd26998c38835552016da79a4dbad0bd9289cc81c29262a0b0fef5fb1e937a5', 1, 3, 'Authentication Token', '[]', 0, '2021-09-29 03:30:05', '2021-09-29 03:30:05', '2022-09-29 10:30:05'),
('eeb62626b4cb1bd2c38670e651f587b8ba8542fda708c26827315b822b889aaa1ef16f7617f1ba71', 1, 3, 'Authentication Token', '[]', 0, '2021-08-03 06:41:16', '2021-08-03 06:41:16', '2022-08-03 13:41:16'),
('eec0d0afc8ec79655007827b4beab62717a6c257fa9797ac130ce6a881e54f088f473ad5d5a93215', 1, 3, 'Authentication Token', '[]', 0, '2022-01-01 08:55:50', '2022-01-01 08:55:50', '2023-01-01 15:55:50'),
('efcb9ff9882f90263ab273c228267d9d03d3fcb3f7935962e0d9e5a9804e203bca11fbfc8c7e2e88', 1, 3, 'Authentication Token', '[]', 0, '2021-12-31 09:56:33', '2021-12-31 09:56:33', '2022-12-31 16:56:33'),
('f1b8df93c9be42d1afbd1c290fd25b8bbee432b3f9f6d006cd8617e912c0cbcf8133da1aea6d9348', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 03:40:51', '2023-01-14 03:40:51', '2024-01-14 10:40:51'),
('f2c41147444cda80fa315d17fd1c089cb742e4b799a98c9d864876a2aaa63bbd0f659b266f9a662c', 1, 3, 'Authentication Token', '[]', 0, '2021-11-22 03:50:03', '2021-11-22 03:50:03', '2022-11-22 10:50:03'),
('f37086a515562d74d33f0290e02c87cd4ac33c6ee7d57bd5f2f0fc7425a8d26b1cecee3a417d4dc3', 1, 3, 'Authentication Token', '[]', 0, '2021-12-03 01:20:29', '2021-12-03 01:20:29', '2022-12-03 08:20:29');
INSERT INTO `oauth_access_tokens` (`id`, `user_id`, `client_id`, `name`, `scopes`, `revoked`, `created_at`, `updated_at`, `expires_at`) VALUES
('f39e56838e79868ee4c51944b2a5526748232ec2659c94a49ed630b075b8cbdf27986c5247997a26', 1, 3, 'Authentication Token', '[]', 0, '2021-10-27 06:43:53', '2021-10-27 06:43:53', '2022-10-27 13:43:53'),
('f4214124567708161456f638503703cb9498528acb954ebfd0e102588620704f88675de93a21298a', 1, 3, 'Authentication Token', '[]', 0, '2021-11-22 03:56:24', '2021-11-22 03:56:24', '2022-11-22 10:56:24'),
('f44f7210c1ee7af2f29bd59a709df3a460c341752d6a45f5a234cb787e7604d5c1027bd9b3a2f00b', 1, 3, 'Authentication Token', '[]', 0, '2022-01-02 06:54:18', '2022-01-02 06:54:18', '2023-01-02 13:54:18'),
('f4611fa0a3ceedc31214a0c62b934b8eb1d4793b5103b608bf44246790bc2fd12497cb37ee4c7e03', 1, 3, 'Authentication Token', '[]', 0, '2023-01-14 03:42:00', '2023-01-14 03:42:00', '2024-01-14 10:42:00'),
('f5391441aa90606fc6c8b26b8ab6e6aba242c54b45dbff635750adabc24a79100e121ffefb43fcc1', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 03:41:05', '2021-08-04 03:41:05', '2022-08-04 10:41:05'),
('f568cf7cb216e0d0c3b4686923d8f9eabf12c20a7e2f3f53b8ff2e8448a8d494ac714025dd444156', 1, 3, 'Authentication Token', '[]', 0, '2021-11-22 03:53:24', '2021-11-22 03:53:24', '2022-11-22 10:53:24'),
('f600dc748ff43f3bf0bf857c2484339e197114964122feb9e29311d49afa01b95e6fe087b7db50e9', 1, 3, 'Authentication Token', '[]', 0, '2021-08-04 03:41:06', '2021-08-04 03:41:06', '2022-08-04 10:41:06'),
('f67019aa5fb0dacc3e96e03b10001fc266a7289588abb7f69160f45a52db12f14ac79cd5d05014b8', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 10:00:33', '2023-01-13 10:00:33', '2024-01-13 17:00:33'),
('f68973f668cacafda7fd8fb04cc9a60f2f037b7df0a165d3047f94d15ef47797f9cb32221eb013c2', 1, 3, 'Authentication Token', '[]', 0, '2021-10-04 16:46:01', '2021-10-04 16:46:01', '2022-10-04 23:46:01'),
('f7d8c6cbd182ee08355a080c27b7a5cf20681a7b289572977d81e3f74287bd88c2288704d060ff3a', 1, 3, 'Authentication Token', '[]', 0, '2021-09-13 19:06:24', '2021-09-13 19:06:24', '2021-09-15 02:06:23'),
('f7f25d1ed91e86ffd8849b904ec90f804657bb2ab027d1f48797cc89b83bb235d9eba7f3d4ba1dc3', 1, 3, 'Authentication Token', '[]', 0, '2022-01-02 08:22:08', '2022-01-02 08:22:08', '2023-01-02 15:22:08'),
('f7f6964e3caf4bfe22166ba8c2fbe0e20a0fc18f3c033b5ae23833e62ad4919011a3f37d0b7a0df7', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 09:34:23', '2023-01-13 09:34:23', '2024-01-13 16:34:23'),
('fbbfac5929997f9c28b9a15f641f941c2ba29336fbc687d0501c601cb50ab788fc14cbf4da7b8eb9', 1, 3, 'Authentication Token', '[]', 0, '2021-09-22 06:29:55', '2021-09-22 06:29:55', '2022-09-22 13:29:55'),
('fcbe408fc381ce67a343add91cc0491a644195f5b3d0c9c67520393741a392bcf5476958fddb5a41', 1, 3, 'Authentication Token', '[]', 0, '2022-01-05 02:22:50', '2022-01-05 02:22:50', '2023-01-05 09:22:50'),
('fd00c98bf112eee5a5deaf5a7989f52374a77ce8a2ef50fb1f3060732654da15a719e6289ff44383', 1, 3, 'Authentication Token', '[]', 0, '2021-12-06 01:21:51', '2021-12-06 01:21:51', '2022-12-06 08:21:51'),
('fd1b3dd4d359e45813a2f8d8a53e2352f59a0d525c486bf16edd99612c8236e4686119385b81bf2a', 1, 3, 'Authentication Token', '[]', 0, '2021-10-19 03:11:52', '2021-10-19 03:11:52', '2022-10-19 10:11:52'),
('fd52d3df38c60bfdf94e51718a52021b3494bac223921881e254d5ea2e92792581fc6bf655332561', 1, 3, 'Authentication Token', '[]', 0, '2021-10-12 05:31:12', '2021-10-12 05:31:12', '2022-10-12 12:31:12'),
('feb71b6af6eba3e18dd908411b3f74309d7b7d6e21de9bede8b0d5d6357247fef92f3c35671a29c2', 1, 3, 'Authentication Token', '[]', 0, '2023-01-13 10:10:36', '2023-01-13 10:10:36', '2024-01-13 17:10:36'),
('ff042e20117cd52d14e4d6a8ae7954c769b3a2578768c0f619cb6fa83f5fa94464b3d30cee59338e', 1, 3, 'Authentication Token', '[]', 0, '2021-09-19 20:20:10', '2021-09-19 20:20:10', '2021-09-21 03:20:09');

-- --------------------------------------------------------

--
-- Struktur dari tabel `oauth_auth_codes`
--

CREATE TABLE `oauth_auth_codes` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `scopes` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `oauth_clients`
--

CREATE TABLE `oauth_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `secret` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `provider` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `redirect` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `personal_access_client` tinyint(1) NOT NULL,
  `password_client` tinyint(1) NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `oauth_clients`
--

INSERT INTO `oauth_clients` (`id`, `user_id`, `name`, `secret`, `provider`, `redirect`, `personal_access_client`, `password_client`, `revoked`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Laravel Personal Access Client', 'hZEvjedt5vnCKjzpegoC4R3eDsSdcre72V6KDL83', NULL, 'http://localhost', 1, 0, 0, '2021-07-31 07:30:36', '2021-07-31 07:30:36'),
(2, NULL, 'Laravel Password Grant Client', 'CElFdBx6EAAULHmtRn0VASh3Mcruv3P9Wg8G78gV', 'users', 'http://localhost', 0, 1, 0, '2021-07-31 07:30:36', '2021-07-31 07:30:36'),
(3, NULL, 'Laravel Personal Access Client', 'GxxOFlGQjkEMUtPwAHM48w7xB9yetZxWuI7tmXKc', NULL, 'http://localhost', 1, 0, 0, '2021-07-31 08:36:18', '2021-07-31 08:36:18'),
(4, NULL, 'Laravel Password Grant Client', '3XJHXBVYL2HlaY4lyYgAailIAV2dds3t3wmF8M35', 'admins', 'http://localhost', 0, 1, 0, '2021-07-31 08:36:29', '2021-07-31 08:36:29');

-- --------------------------------------------------------

--
-- Struktur dari tabel `oauth_personal_access_clients`
--

CREATE TABLE `oauth_personal_access_clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data untuk tabel `oauth_personal_access_clients`
--

INSERT INTO `oauth_personal_access_clients` (`id`, `client_id`, `created_at`, `updated_at`) VALUES
(1, 1, '2021-07-31 07:30:36', '2021-07-31 07:30:36'),
(2, 3, '2021-07-31 08:36:18', '2021-07-31 08:36:18');

-- --------------------------------------------------------

--
-- Struktur dari tabel `oauth_refresh_tokens`
--

CREATE TABLE `oauth_refresh_tokens` (
  `id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `access_token_id` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `revoked` tinyint(1) NOT NULL,
  `expires_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `package`
--

CREATE TABLE `package` (
  `ID_PACKAGE` int(11) NOT NULL,
  `ID_USERS` varchar(30) NOT NULL,
  `ALAMAT` varchar(255) NOT NULL,
  `JAM` time NOT NULL,
  `TANGGAL` date NOT NULL,
  `PENERIMA` varchar(200) NOT NULL,
  `KODE_KIRIM` varchar(200) DEFAULT NULL,
  `ISI_PAKET` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL,
  `ISI_PAKET_NAME` longtext DEFAULT NULL,
  `ID_PERIOD` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Struktur dari tabel `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Struktur dari tabel `redeem_log`
--

CREATE TABLE `redeem_log` (
  `ID_REDEEM_LOG` int(11) NOT NULL,
  `ID_CATALOGUE` int(11) DEFAULT NULL,
  `ID_USERS` varchar(30) DEFAULT NULL,
  `ID_REDEEM_STATUS` int(11) DEFAULT NULL,
  `REDEEM_KEY` varchar(200) NOT NULL,
  `REDEEM_TIME` timestamp NULL DEFAULT NULL,
  `REDEEM_FINISHED_TIME` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `redeem_status`
--

CREATE TABLE `redeem_status` (
  `ID_REDEEM_STATUS` int(11) NOT NULL,
  `REDEEM_STATUS` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `redeem_status`
--

INSERT INTO `redeem_status` (`ID_REDEEM_STATUS`, `REDEEM_STATUS`) VALUES
(1, 'pending'),
(2, 'finish'),
(3, 'on process');

-- --------------------------------------------------------

--
-- Struktur dari tabel `users`
--

CREATE TABLE `users` (
  `ID_USERS` varchar(30) NOT NULL,
  `NAME` varchar(200) DEFAULT NULL,
  `PRODI` varchar(150) NOT NULL,
  `FAKULTAS` varchar(150) NOT NULL,
  `PHOTO` varchar(255) DEFAULT NULL,
  `PHOTO_THUMB` varchar(255) DEFAULT NULL,
  `POINTS` int(11) DEFAULT NULL,
  `INFO` varchar(250) DEFAULT NULL,
  `EMAIL` varchar(200) DEFAULT NULL,
  `TYPE` varchar(30) NOT NULL,
  `CREATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UPDATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `users_backup`
--

CREATE TABLE `users_backup` (
  `ID_USERS` varchar(30) NOT NULL,
  `NAME` varchar(200) DEFAULT NULL,
  `PRODI` varchar(150) NOT NULL,
  `FAKULTAS` varchar(150) NOT NULL,
  `PHOTO` varchar(255) DEFAULT NULL,
  `PHOTO_THUMB` varchar(255) DEFAULT NULL,
  `POINTS` int(11) DEFAULT NULL,
  `INFO` varchar(250) DEFAULT NULL,
  `EMAIL` varchar(200) DEFAULT NULL,
  `TYPE` varchar(30) NOT NULL,
  `CREATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UPDATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_device`
--

CREATE TABLE `user_device` (
  `ID_USER_DEVICE` int(11) NOT NULL,
  `ID_USERS` varchar(30) NOT NULL,
  `API_KEY` varchar(250) DEFAULT NULL,
  `UUID` varchar(250) DEFAULT NULL,
  `GCM` varchar(250) DEFAULT NULL,
  `DEVICE_ID` varchar(250) DEFAULT NULL,
  `DEVICE_TYPE` varchar(50) DEFAULT NULL,
  `DEVICE_MODEL` varchar(250) DEFAULT NULL,
  `VERSION_CODE` int(11) DEFAULT NULL,
  `CREATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UPDATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_review`
--

CREATE TABLE `user_review` (
  `ID` int(11) NOT NULL,
  `DEVICE` varchar(50) DEFAULT '0',
  `ID_USERS` varchar(100) DEFAULT '0',
  `REVIEW` varchar(255) DEFAULT '0',
  `VERSION` varchar(50) DEFAULT '0',
  `TIME_CREATED` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_social`
--

CREATE TABLE `user_social` (
  `ID_USER_SOCIAL` int(11) NOT NULL,
  `ID_USERS` varchar(30) DEFAULT NULL,
  `SOCIAL_TYPE` varchar(100) DEFAULT NULL,
  `SOCIAL_TOKEN` varchar(250) DEFAULT NULL,
  `TOKEN_SECRET` varchar(250) NOT NULL,
  `ACTIVE_STATUS` tinyint(1) NOT NULL,
  `CREATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `UPDATE_TIME` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Struktur dari tabel `user_type`
--

CREATE TABLE `user_type` (
  `ID` int(11) NOT NULL,
  `TYPE` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `user_type`
--

INSERT INTO `user_type` (`ID`, `TYPE`) VALUES
(1, 'student'),
(2, 'teacher'),
(3, 'all user');

-- --------------------------------------------------------

--
-- Struktur dari tabel `year_period`
--

CREATE TABLE `year_period` (
  `ID_PERIOD` int(11) NOT NULL,
  `YEAR_PERIODE` varchar(20) NOT NULL,
  `STATUS` varchar(30) NOT NULL,
  `YEAR_START` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `YEAR_FINISH` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

--
-- Dumping data untuk tabel `year_period`
--

INSERT INTO `year_period` (`ID_PERIOD`, `YEAR_PERIODE`, `STATUS`, `YEAR_START`, `YEAR_FINISH`) VALUES
(1, '1', 'inactive', '2018-08-04 17:00:00', '2019-07-10 17:00:00'),
(2, '', 'inactive', '2019-08-11 17:00:00', '2020-08-06 17:00:00'),
(4, '', 'inactive', '2020-08-23 17:00:00', '2021-07-30 17:00:00'),
(5, '', 'active', '2021-07-31 17:00:00', '2022-07-30 17:00:00');

--
-- Indexes for dumped tables
--

--
-- Indeks untuk tabel `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `catalogue`
--
ALTER TABLE `catalogue`
  ADD PRIMARY KEY (`ID_CATALOGUE`),
  ADD KEY `fk_cat_per` (`ID_PERIOD`),
  ADD KEY `fk_cat_cattype` (`ID_CTG_TYPE`);

--
-- Indeks untuk tabel `catalogue_type`
--
ALTER TABLE `catalogue_type`
  ADD PRIMARY KEY (`ID_CTG_TYPE`);

--
-- Indeks untuk tabel `device_version`
--
ALTER TABLE `device_version`
  ADD PRIMARY KEY (`ID_DEVICE_VERSION`);

--
-- Indeks untuk tabel `elearning_challenge`
--
ALTER TABLE `elearning_challenge`
  ADD PRIMARY KEY (`ID_ELEARNING_CHALLENGE`),
  ADD KEY `fk_elearn_yperiod` (`ID_PERIOD`);

--
-- Indeks untuk tabel `elearning_challenge_backup`
--
ALTER TABLE `elearning_challenge_backup`
  ADD PRIMARY KEY (`ID_ELEARNING_CHALLENGE`),
  ADD KEY `fk_elearn_yperiod` (`ID_PERIOD`);

--
-- Indeks untuk tabel `elearning_history`
--
ALTER TABLE `elearning_history`
  ADD PRIMARY KEY (`ID_ELEARNING_HISTORY`),
  ADD KEY `fk_elhis_users` (`ID_USERS`),
  ADD KEY `fk_elhis_elchall` (`ID_ELEARNING_CHALLENGE`);

--
-- Indeks untuk tabel `elearning_history_backup`
--
ALTER TABLE `elearning_history_backup`
  ADD PRIMARY KEY (`ID_ELEARNING_HISTORY`),
  ADD KEY `fk_elhis_users` (`ID_USERS`),
  ADD KEY `fk_elhis_elchall` (`ID_ELEARNING_CHALLENGE`);

--
-- Indeks untuk tabel `elearning_history_original`
--
ALTER TABLE `elearning_history_original`
  ADD PRIMARY KEY (`ID_ELEARNING_HISTORY`),
  ADD KEY `fk_elhis_users` (`ID_USERS`),
  ADD KEY `fk_elhis_elchall` (`ID_ELEARNING_CHALLENGE`);

--
-- Indeks untuk tabel `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`ID_EVENTS`),
  ADD KEY `fk_event_per` (`ID_PERIOD`),
  ADD KEY `fk_event_evtype` (`ID_EVENT_TYPE`);

--
-- Indeks untuk tabel `event_detail`
--
ALTER TABLE `event_detail`
  ADD PRIMARY KEY (`ID_EVENT_DETAIL`),
  ADD KEY `FK_RELATIONSHIP_4` (`ID_EVENTS`),
  ADD KEY `fk_evdet_evrole` (`ID_EVENT_ROLE`);

--
-- Indeks untuk tabel `event_role`
--
ALTER TABLE `event_role`
  ADD PRIMARY KEY (`ID_EVENT_ROLE`);

--
-- Indeks untuk tabel `event_type`
--
ALTER TABLE `event_type`
  ADD PRIMARY KEY (`ID_EVENT_TYPE`);

--
-- Indeks untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indeks untuk tabel `helper`
--
ALTER TABLE `helper`
  ADD PRIMARY KEY (`NAME`);

--
-- Indeks untuk tabel `history`
--
ALTER TABLE `history`
  ADD PRIMARY KEY (`ID_HISTORY`),
  ADD KEY `fk_hist_user` (`ID_USERS`),
  ADD KEY `fk_his_event` (`ID_EVENTS`);

--
-- Indeks untuk tabel `history_detail`
--
ALTER TABLE `history_detail`
  ADD PRIMARY KEY (`ID_HISTORY_DETAIL`),
  ADD KEY `fk_dethis_his` (`ID_HISTORY`),
  ADD KEY `fk_dethis_evdet` (`ID_EVENT_DETAIL`);

--
-- Indeks untuk tabel `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indeks untuk tabel `last_refresh_elearning_point`
--
ALTER TABLE `last_refresh_elearning_point`
  ADD PRIMARY KEY (`NUMBER`),
  ADD KEY `fk_lstref_users` (`ID_USERS`);

--
-- Indeks untuk tabel `mdl_event_list`
--
ALTER TABLE `mdl_event_list`
  ADD PRIMARY KEY (`ID_MDL_EVENT_LIST`);

--
-- Indeks untuk tabel `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `missmatch_elearning_history`
--
ALTER TABLE `missmatch_elearning_history`
  ADD PRIMARY KEY (`ID_MISSMATCH_ELEARNING_HISTORY`);

--
-- Indeks untuk tabel `news`
--
ALTER TABLE `news`
  ADD PRIMARY KEY (`ID_NEWS`);

--
-- Indeks untuk tabel `oauth_access_tokens`
--
ALTER TABLE `oauth_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_access_tokens_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `oauth_auth_codes`
--
ALTER TABLE `oauth_auth_codes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_auth_codes_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `oauth_clients`
--
ALTER TABLE `oauth_clients`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_clients_user_id_index` (`user_id`);

--
-- Indeks untuk tabel `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  ADD PRIMARY KEY (`id`);

--
-- Indeks untuk tabel `oauth_refresh_tokens`
--
ALTER TABLE `oauth_refresh_tokens`
  ADD PRIMARY KEY (`id`),
  ADD KEY `oauth_refresh_tokens_access_token_id_index` (`access_token_id`);

--
-- Indeks untuk tabel `package`
--
ALTER TABLE `package`
  ADD PRIMARY KEY (`ID_PACKAGE`);

--
-- Indeks untuk tabel `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indeks untuk tabel `redeem_log`
--
ALTER TABLE `redeem_log`
  ADD PRIMARY KEY (`ID_REDEEM_LOG`),
  ADD KEY `fk_redlog_users` (`ID_USERS`),
  ADD KEY `fk_redlog_redstat` (`ID_REDEEM_STATUS`),
  ADD KEY `fk_redlog_cat` (`ID_CATALOGUE`);

--
-- Indeks untuk tabel `redeem_status`
--
ALTER TABLE `redeem_status`
  ADD PRIMARY KEY (`ID_REDEEM_STATUS`),
  ADD UNIQUE KEY `ID_REDEEM_STATUS` (`ID_REDEEM_STATUS`),
  ADD KEY `ID_REDEEM_STATUS_2` (`ID_REDEEM_STATUS`);

--
-- Indeks untuk tabel `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`ID_USERS`);

--
-- Indeks untuk tabel `users_backup`
--
ALTER TABLE `users_backup`
  ADD PRIMARY KEY (`ID_USERS`);

--
-- Indeks untuk tabel `user_device`
--
ALTER TABLE `user_device`
  ADD PRIMARY KEY (`ID_USER_DEVICE`),
  ADD KEY `fk_usrdev_user` (`ID_USERS`);

--
-- Indeks untuk tabel `user_review`
--
ALTER TABLE `user_review`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks untuk tabel `user_social`
--
ALTER TABLE `user_social`
  ADD PRIMARY KEY (`ID_USER_SOCIAL`),
  ADD KEY `fk_usrsoc_users` (`ID_USERS`);

--
-- Indeks untuk tabel `user_type`
--
ALTER TABLE `user_type`
  ADD PRIMARY KEY (`ID`);

--
-- Indeks untuk tabel `year_period`
--
ALTER TABLE `year_period`
  ADD PRIMARY KEY (`ID_PERIOD`);

--
-- AUTO_INCREMENT untuk tabel yang dibuang
--

--
-- AUTO_INCREMENT untuk tabel `admins`
--
ALTER TABLE `admins`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `catalogue`
--
ALTER TABLE `catalogue`
  MODIFY `ID_CATALOGUE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `catalogue_type`
--
ALTER TABLE `catalogue_type`
  MODIFY `ID_CTG_TYPE` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `device_version`
--
ALTER TABLE `device_version`
  MODIFY `ID_DEVICE_VERSION` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `elearning_challenge`
--
ALTER TABLE `elearning_challenge`
  MODIFY `ID_ELEARNING_CHALLENGE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `elearning_challenge_backup`
--
ALTER TABLE `elearning_challenge_backup`
  MODIFY `ID_ELEARNING_CHALLENGE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `elearning_history`
--
ALTER TABLE `elearning_history`
  MODIFY `ID_ELEARNING_HISTORY` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `elearning_history_backup`
--
ALTER TABLE `elearning_history_backup`
  MODIFY `ID_ELEARNING_HISTORY` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `elearning_history_original`
--
ALTER TABLE `elearning_history_original`
  MODIFY `ID_ELEARNING_HISTORY` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `events`
--
ALTER TABLE `events`
  MODIFY `ID_EVENTS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `event_detail`
--
ALTER TABLE `event_detail`
  MODIFY `ID_EVENT_DETAIL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT untuk tabel `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `history`
--
ALTER TABLE `history`
  MODIFY `ID_HISTORY` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `history_detail`
--
ALTER TABLE `history_detail`
  MODIFY `ID_HISTORY_DETAIL` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT untuk tabel `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `last_refresh_elearning_point`
--
ALTER TABLE `last_refresh_elearning_point`
  MODIFY `NUMBER` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `mdl_event_list`
--
ALTER TABLE `mdl_event_list`
  MODIFY `ID_MDL_EVENT_LIST` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `messages`
--
ALTER TABLE `messages`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT untuk tabel `missmatch_elearning_history`
--
ALTER TABLE `missmatch_elearning_history`
  MODIFY `ID_MISSMATCH_ELEARNING_HISTORY` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `news`
--
ALTER TABLE `news`
  MODIFY `ID_NEWS` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `oauth_clients`
--
ALTER TABLE `oauth_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT untuk tabel `oauth_personal_access_clients`
--
ALTER TABLE `oauth_personal_access_clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT untuk tabel `package`
--
ALTER TABLE `package`
  MODIFY `ID_PACKAGE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `redeem_log`
--
ALTER TABLE `redeem_log`
  MODIFY `ID_REDEEM_LOG` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `redeem_status`
--
ALTER TABLE `redeem_status`
  MODIFY `ID_REDEEM_STATUS` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `user_device`
--
ALTER TABLE `user_device`
  MODIFY `ID_USER_DEVICE` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user_review`
--
ALTER TABLE `user_review`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user_social`
--
ALTER TABLE `user_social`
  MODIFY `ID_USER_SOCIAL` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT untuk tabel `user_type`
--
ALTER TABLE `user_type`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT untuk tabel `year_period`
--
ALTER TABLE `year_period`
  MODIFY `ID_PERIOD` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Ketidakleluasaan untuk tabel pelimpahan (Dumped Tables)
--

--
-- Ketidakleluasaan untuk tabel `catalogue`
--
ALTER TABLE `catalogue`
  ADD CONSTRAINT `fk_cat_cattype` FOREIGN KEY (`ID_CTG_TYPE`) REFERENCES `catalogue_type` (`ID_CTG_TYPE`),
  ADD CONSTRAINT `fk_cat_per` FOREIGN KEY (`ID_PERIOD`) REFERENCES `year_period` (`ID_PERIOD`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `elearning_challenge`
--
ALTER TABLE `elearning_challenge`
  ADD CONSTRAINT `fk_elearn_yperiod` FOREIGN KEY (`ID_PERIOD`) REFERENCES `year_period` (`ID_PERIOD`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `elearning_history_original`
--
ALTER TABLE `elearning_history_original`
  ADD CONSTRAINT `fk_elhis_elchall` FOREIGN KEY (`ID_ELEARNING_CHALLENGE`) REFERENCES `elearning_challenge` (`ID_ELEARNING_CHALLENGE`),
  ADD CONSTRAINT `fk_elhis_users` FOREIGN KEY (`ID_USERS`) REFERENCES `users` (`ID_USERS`);

--
-- Ketidakleluasaan untuk tabel `events`
--
ALTER TABLE `events`
  ADD CONSTRAINT `fk_event_evtype` FOREIGN KEY (`ID_EVENT_TYPE`) REFERENCES `event_type` (`ID_EVENT_TYPE`),
  ADD CONSTRAINT `fk_event_per` FOREIGN KEY (`ID_PERIOD`) REFERENCES `year_period` (`ID_PERIOD`) ON DELETE CASCADE;

--
-- Ketidakleluasaan untuk tabel `history`
--
ALTER TABLE `history`
  ADD CONSTRAINT `fk_his_event` FOREIGN KEY (`ID_EVENTS`) REFERENCES `events` (`ID_EVENTS`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_hist_user` FOREIGN KEY (`ID_USERS`) REFERENCES `users` (`ID_USERS`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
