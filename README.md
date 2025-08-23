# Discogs Bundle Demo

> ⚠️ **DEMO APPLICATION NOTICE**  
> This is a demonstration application for educational and testing purposes only. **NOT intended for production use**.  
> Please respect Discogs' terms of service and implement proper caching to avoid overloading their API servers.  
> For production applications, ensure you comply with [Discogs API Guidelines](https://www.discogs.com/developers/#page:home,header:home-general-guidelines).

This is a comprehensive demo of the [calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle)  
in combination with [hwi/HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle).

You need PHP 8.1–8.5 and Symfony 6.4 (LTS), 7.x, or 8.0 (beta).

## Features

🔍 **Search** – Search the entire Discogs database  
👤 **User Profiles** – View complete user profiles with statistics  
📀 **Collections** – Browse user collections with detailed information  
💝 **Wantlists** – View and manage Discogs wantlists  
🎵 **Artist Details** – Comprehensive artist information with discography  
💿 **Release Details** – Complete release information including tracklists  
🏷️ **Label Information** – Detailed label profiles and releases  
📋 **User Lists** – Custom user-created lists and list items  
🛒 **Marketplace** – Browse marketplace listings and inventory  
📦 **Orders** – View and manage marketplace orders  
🎲 **Random Discovery** – Discover random releases from popular artists

## Installation

Make sure Composer is installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

Open a command console, enter your desired parent directory for the demo, and execute:

```console
git clone https://github.com/calliostro/discogs-bundle-demo
cd discogs-bundle-demo
composer install --no-interaction
```

The lock files (`composer.lock`, `symfony.lock`) are not included in this repository to ensure maximum flexibility for different PHP and Symfony versions. After cloning, please run `composer install` or `composer update` to install the appropriate package versions and Symfony recipes for your environment.

If you want to suppress the interactive recipe prompt ("Do you want to execute this recipe?"), use Composer with the `--no-interaction` flag.

## Configuration

First, you must register the application at https://www.discogs.com/applications/edit to get the `consumer_key` and
`consumer_secret`. You can set the values as environment variables. Then they will be used by both bundles. See also
[calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle#configuration).

## Usage

Start the local web server of Symfony in the discogs-bundle-demo directory:

```console
symfony serve
```

Or use PHP's built-in server:

```console
php -S 127.0.0.1:8000 -t public
```

Open your web browser and visit the URL shown in the console output.

## 📋 Available Routes

The demo provides the following routes to showcase different Discogs API features:

### 🛣️ Main Routes

| Route           | Method   | Description                                                        |
|-----------------|----------|--------------------------------------------------------------------|
| `/`             | GET      | 🏠 Home page with user authentication info and navigation          |
| `/search`       | GET/POST | 🔍 Search the Discogs database for artists, releases, labels, etc. |
| `/artist/{id}`  | GET      | 🎵 Display detailed artist information and releases                |
| `/release/{id}` | GET      | 💿 Display detailed release information including tracklist        |
| `/label/{id}`   | GET      | 🏷️ Display detailed label information and releases                |
| `/master/{id}`  | GET      | 🎯 Display master release information and versions                 |
| `/collection`   | GET      | 📀 View the authenticated user's Discogs collection                |
| `/wantlist`     | GET      | 💝 View the authenticated user's Discogs wantlist                  |
| `/profile`      | GET      | 👤 View the authenticated user's complete Discogs profile          |
| `/lists`        | GET      | 📋 View the authenticated user's custom lists                      |
| `/list/{id}`    | GET      | 📝 View items in a specific user list                               |
| `/marketplace`  | GET      | 🛒 Browse marketplace listings                                     |
| `/orders`       | GET      | 📦 View and manage marketplace orders                              |
| `/inventory`    | GET      | 📦 View user's marketplace inventory                               |
| `/random`       | GET      | 🎲 Discover random releases from popular artists                   |

### 🎯 Route Examples

🎵 **Artist Details**: `/artist/8760` (Pink Floyd)  
💿 **Release Details**: `/release/1` (First release in the Discogs database)  
🏷️ **Label Details**: `/label/1` (Blue Note Records)  
📀 **Master Release**: `/master/5427` (Dark Side of the Moon)

**🔍 Search Examples**
- `/search?q=Pink Floyd&type=artist`
- `/search?q=Abbey Road&type=release`
- `/search?q=Blue Note&type=label`

## API Features Demonstrated

Each route demonstrates different aspects of the Discogs API:

### Search (`/search`)
📊 Database search with filtering by type  
📄 Pagination support  
🔍 Multiple search types (artist, release, label, master)

### Artist Details (`/artist/{id}`)
🎤 `getArtist()` - Basic artist information  
💽 `getArtistReleases()` - Artist's discography  
🖼️ Artist images, aliases, and band members

### Release Details (`/release/{id}`)
📀 `getRelease()` - Complete release information  
🎵 Tracklist with track details  
👥 Credits and contributors  
🏷️ Label information and identifiers

### Label Details (`/label/{id}`)
🏢 `getLabel()` - Complete label information  
📀 `getLabelReleases()` - Label's catalog  
📈 Label statistics and history

### Master Release (`/master/{id}`)
🎯 `getMaster()` - Master release information  
📑 `getMasterVersions()` - All versions and pressings  
💿 Format variations and differences

### User Features (`/collection`, `/wantlist`, `/profile`)
🔐 `getOAuthIdentity()` - OAuth user authentication  
📁 `getCollectionFolders()` - Collection organization  
📀 `getCollectionItemsByFolder()` - Collection contents  
💝 `getWantlist()` - User's wanted items  
👤 `getProfile()` - Complete user profile with statistics

### List Management (`/lists`, `/list/{id}`)
📋 `getUserLists()` - User's custom lists  
📝 `getLists()` - List items and details  
🗂️ List organization and management

### Marketplace (`/marketplace`, `/orders`, `/inventory`)
🛒 `getInventory()` - Marketplace listings  
📦 `getOrders()` - Order management  
💰 `getOrder()` - Detailed order information  
📊 Marketplace statistics and insights

## 🔧 Code Examples

### 🎵 Basic Artist Information
```php
#[Route('/artist/{id}', name: 'artist_detail', methods: ['GET'])]
public function artistDetail(DiscogsClient $discogs, int $id): Response
{
    $artist = $discogs->getArtist(['id' => $id]);
    $artistReleases = $discogs->getArtistReleases(['id' => $id, 'per_page' => 10]);

    return $this->render('artist_detail.html.twig', [
        'artist' => $artist,
        'releases' => $artistReleases
    ]);
}
```

### 🏷️ Label Information
```php
#[Route('/label/{id}', name: 'label_detail', methods: ['GET'])]
public function labelDetail(DiscogsClient $discogs, int $id): Response
{
    $label = $discogs->getLabel(['id' => $id]);
    $labelReleases = $discogs->getLabelReleases(['id' => $id, 'per_page' => 10]);

    return $this->render('label_detail.html.twig', [
        'label' => $label,
        'releases' => $labelReleases
    ]);
}
```

### 📋 User Lists Management
```php
#[Route('/lists', name: 'user_lists', methods: ['GET'])]
public function userLists(DiscogsClient $discogs): Response
{
    $identity = $discogs->getOAuthIdentity();
    $userLists = $discogs->getUserLists([
        'username' => $identity['username'],
        'per_page' => 50
    ]);

    return $this->render('user_lists.html.twig', [
        'lists' => $userLists,
        'username' => $identity['username']
    ]);
}
```

### 🛒 Marketplace Integration
```php
#[Route('/marketplace', name: 'marketplace', methods: ['GET'])]
public function marketplace(DiscogsClient $discogs, Request $request): Response
{
    $searchQuery = $request->query->get('q', '');
    $listings = null;

    if ($searchQuery) {
        $searchResults = $discogs->search([
            'q' => $searchQuery,
            'type' => 'release',
            'per_page' => 20
        ]);
        $listings = $searchResults;
    }

    return $this->render('marketplace.html.twig', [
        'listings' => $listings,
        'searchQuery' => $searchQuery
    ]);
}
```

## Technical Implementation

### Modern PHP Features
🚀 **PHP 8 Attributes** for routing (instead of annotations)  
🔒 **Strong typing** with proper return types  
⚡ **Exception handling** with user-friendly error messages  
🏗️ **Modern PHP 8.1–8.5** compatibility

### UI/UX Features
🎨 **Bootstrap 5** for responsive design  
🃏 **Interactive cards** with hover effects  
🧭 **Breadcrumb navigation** for better user experience  
💬 **Flash messages** for user feedback  
📄 **Pagination** for large result sets

### Framework Support
🎯 **Symfony 6.4 (LTS)** - Long-term support  
⚡ **Symfony 7.x** — Latest stable features  
🧪 **Symfony 8.0 (beta)** - Cutting-edge features

### API Integration
🔐 **OAuth authentication** flow  
⏱️ **Rate limiting** awareness  
🛡️ **Error handling** for API failures  
🚀 **Caching-friendly** implementation

### Production Guidelines
⚠️ **Important for Production Use:**
- Implement proper caching to reduce API calls
- Respect Discogs' rate limits (60 requests per minute for authenticated requests)
- Cache search results and static data (artists, releases, labels)
- Use appropriate cache TTL values (e.g., 24h for releases, 1h for dynamic data)
- Consider implementing request queuing for high-traffic applications
- Monitor your API usage through Discogs developer dashboard

## Documentation

Further documentation can be found at:  
🔗 [Discogs API v2.0 Documentation](https://www.discogs.com/developers)  
📦 [calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle)  
🔧 [calliostro/php-discogs-api](https://github.com/calliostro/php-discogs-api)
