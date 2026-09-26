# Discogs Bundle Demo (Legacy v3.x)

[![License](https://poser.pugx.org/calliostro/discogs-bundle/license)](https://packagist.org/packages/calliostro/discogs-bundle)
[![PHP Version](https://img.shields.io/badge/php-%5E8.1-blue.svg)](https://php.net)
[![Symfony Version](https://img.shields.io/badge/symfony-6.4%20%7C%207.x-blue.svg)](https://symfony.com)

> [!WARNING]
> **Legacy Reference Application (`discogs-bundle` v3.x)**
>
> This demo project is maintained as a legacy reference specifically for **[`calliostro/discogs-bundle` v3.x](https://github.com/calliostro/discogs-bundle/tree/3.x)** and OAuth 1.0a flow with `hwi/HWIOAuthBundle`.
>
> In **`calliostro/discogs-bundle` v4+**, OAuth authentication is natively built-in directly in the bundle and in the underlying **[`calliostro/php-discogs-api`](https://github.com/calliostro/php-discogs-api)** client (eliminating the need for an external OAuth bundle). For new projects, please refer to the current **[`calliostro/discogs-bundle` (v4+)](https://github.com/calliostro/discogs-bundle)**.

> [!NOTE]
> **Educational & Testing Showcase**
>
> This application is a developer sandbox demonstrating how to wire and consume the Discogs API in a Symfony application. It is **not intended for production use**. Always adhere to the [Discogs API Guidelines](https://www.discogs.com/developers/#page:home,header:home-general-guidelines) and implement proper client caching to avoid hitting rate limits.

A Symfony demo application showcasing the integration of [`calliostro/discogs-bundle`](https://github.com/calliostro/discogs-bundle/tree/3.x) with [`hwi/HWIOAuthBundle`](https://github.com/hwi/HWIOAuthBundle) for PHP 8.1+ and Symfony 6.4 (LTS) and 7.x.

---

## 📦 Installation

Make sure Composer is installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

Clone the repository and install the dependencies:

```bash
git clone https://github.com/calliostro/discogs-bundle-demo.git
cd discogs-bundle-demo
composer install --no-interaction
```

> [!TIP]
> Lock files (`composer.lock`, `symfony.lock`) are intentionally omitted to give maximum flexibility across PHP and Symfony versions. After cloning, `composer install` will resolve the appropriate package versions and recipes for your environment.

---

## ⚙️ Configuration

### 1. Register an Application on Discogs

Create a new developer application at [Discogs Developer Applications](https://www.discogs.com/applications/edit) (login required) and fill in the form:

| Field | Value / Suggestion | Notes |
|:------|:-------------------|:------|
| **Application Name** | `Discogs Bundle Demo` | Any descriptive name |
| **Description** | `Demo application for testing calliostro/discogs-bundle` | Brief summary of purpose |
| **Homepage URL** *(optional)* | `http://127.0.0.1:8000` | Your local development URL |
| **Callback URL** *(optional)* | `http://127.0.0.1:8000/login/check-discogs` | OAuth 1.0a callback endpoint |

Once registered, Discogs will generate your **Consumer Key** and **Consumer Secret**.

### 2. Configure Credentials

Add your credentials to `.env` (or `.env.local`):

```ini
###> hwi/oauth-bundle ###
CONSUMER_KEY=your_discogs_consumer_key
CONSUMER_SECRET=your_discogs_consumer_secret
###< hwi/oauth-bundle ###
```

These environment variables are shared and used by both `calliostro/discogs-bundle` and `hwi/HWIOAuthBundle`.

---

## 🚀 Getting Started

Start the Symfony local web server:

```bash
symfony serve
```

Or use PHP's built-in web server:

```bash
php -S 127.0.0.1:8000 -t public
```

Open your browser and navigate to `http://127.0.0.1:8000`.

---

## 🛠️ Demonstrated API Endpoints & Routes

The demo application showcases various Discogs API endpoints through dedicated controllers and Twig templates:

| Route | Method | Description | Demonstrated Client Methods |
|:------|:-------|:------------|:----------------------------|
| `/` | GET | Dashboard with OAuth authentication status | `getOAuthIdentity()` |
| `/search` | GET/POST | Database search with filtering and pagination | `search()` |
| `/artist/{id}` | GET | Artist profile, discography, and band members | `getArtist()`, `getArtistReleases()` |
| `/release/{id}` | GET | Release metadata, tracklist, and credits | `getRelease()` |
| `/label/{id}` | GET | Label profile and releases | `getLabel()`, `getLabelReleases()` |
| `/master/{id}` | GET | Master release details and versions | `getMaster()`, `getMasterVersions()` |
| `/collection` | GET | User collection folders and items | `getCollectionFolders()`, `getCollectionItemsByFolder()` |
| `/wantlist` | GET | User wantlist items | `getWantlist()` |
| `/profile` | GET | User profile and statistics | `getProfile()` |
| `/lists` | GET | Custom user lists overview | `getUserLists()` |
| `/list/{id}` | GET | User list item details | `getLists()` |
| `/marketplace` | GET | Marketplace listings lookup | `search()`, `getInventory()` |
| `/orders` | GET | Marketplace orders and details | `getOrders()`, `getOrder()` |
| `/random` | GET | Random release discovery | `getArtistReleases()` |

### Quick Test Examples

- **Artist Profile:** `/artist/8760` (Pink Floyd)
- **Release Details:** `/release/1` (The Persuader - Stockholmsnatt)
- **Label Catalog:** `/label/1` (Blue Note Records)
- **Master Release:** `/master/5427` (Pink Floyd - The Dark Side of the Moon)

---

## 🔧 Controller Code Examples

### 🎵 Artist Details & Discography

```php
#[Route('/artist/{id}', name: 'artist_detail', methods: ['GET'])]
public function artistDetail(DiscogsClient $discogs, int $id): Response
{
    $artist = $discogs->getArtist(['id' => $id]);
    $artistReleases = $discogs->getArtistReleases(['id' => $id, 'per_page' => 10]);

    return $this->render('artist_detail.html.twig', [
        'artist' => $artist,
        'releases' => $artistReleases,
    ]);
}
```

### 🏷️ Label Details & Releases

```php
#[Route('/label/{id}', name: 'label_detail', methods: ['GET'])]
public function labelDetail(DiscogsClient $discogs, int $id): Response
{
    $label = $discogs->getLabel(['id' => $id]);
    $labelReleases = $discogs->getLabelReleases(['id' => $id, 'per_page' => 10]);

    return $this->render('label_detail.html.twig', [
        'label' => $label,
        'releases' => $labelReleases,
    ]);
}
```

### 📋 User Lists Integration

```php
#[Route('/lists', name: 'user_lists', methods: ['GET'])]
public function userLists(DiscogsClient $discogs): Response
{
    $identity = $discogs->getOAuthIdentity();
    $userLists = $discogs->getUserLists([
        'username' => $identity['username'],
        'per_page' => 50,
    ]);

    return $this->render('user_lists.html.twig', [
        'lists' => $userLists,
        'username' => $identity['username'],
    ]);
}
```

---

## 📋 Requirements

- **PHP** `^8.1` (tested on PHP 8.1–8.6)
- **Symfony** `^6.4 || ^7.x`
- **calliostro/discogs-bundle** `^3.1`
- **hwi/oauth-bundle** `^2.0`

---

## 📄 License

This project is licensed under the MIT License — see the [LICENSE](LICENSE) file for details.

---

## ⚖️ Disclaimer

Discogs is a registered trademark of Zink Media, LLC. This project is an independent, unofficial open-source library and is not affiliated with, endorsed by, or sponsored by Discogs or Zink Media, LLC.

---

## 🙏 Acknowledgments

- [Discogs](https://www.discogs.com/) for providing the database and API.
- [Symfony](https://symfony.com) for the web framework and dependency injection container.
- Main Bundle: [`calliostro/discogs-bundle`](https://github.com/calliostro/discogs-bundle).
- Underlying API Client: [`calliostro/php-discogs-api`](https://github.com/calliostro/php-discogs-api).
