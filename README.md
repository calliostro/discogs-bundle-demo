Discogs Bundle Demo
===================

This is a comprehensive demo of the [calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle)
in combination with [hwi/HWIOAuthBundle](https://github.com/hwi/HWIOAuthBundle).

You need PHP 8.1–8.4 and Symfony 6.4 or 7.

The lock files (`composer.lock`, `symfony.lock`) are not included in this repository to ensure maximum flexibility for different PHP and Symfony versions. After cloning, please run `composer install` or `composer update` to install the appropriate package versions and Symfony recipes for your environment.

If you want to suppress the interactive recipe prompt ("Do you want to execute this recipe?"), use Composer with the `--no-interaction` flag:

You can also run the installation with the `--no-interaction` flag to automatically skip the recipe prompt.

This demo showcases the full capabilities of the Discogs API through a modern web interface:

## Features

- 🔍 **Search** - Search the entire Discogs database
- 👤 **User Profiles** - View complete user profiles with statistics
- 📀 **Collections** - Browse user collections with detailed information
- 💝 **Wantlists** - View and manage Discogs wantlists
- 🎵 **Artist Details** - Comprehensive artist information with discography
- 💿 **Release Details** - Complete release information including tracklists
- 🎲 **Random Discovery** - Discover random releases from popular artists

## Installation

Make sure Composer is installed globally, as explained in the [installation chapter](https://getcomposer.org/doc/00-intro.md) of the Composer documentation.

Open a command console, enter your desired parent directory for the demo and execute:

```console
git clone https://github.com/calliostro/discogs-bundle-demo
cd discogs-bundle-demo
composer install --no-interaction
```

## Configuration

First, you must register the application at https://www.discogs.com/applications/edit to get the `consumer_key` and
`consumer_secret`. You can set the values as environment variables. Then they will be used by both bundles. See also
[calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle#configuration).

## Available Routes

The demo provides the following routes to showcase different Discogs API features:

### Main Routes

| Route | Method | Description |
|-------|--------|-------------|
| `/` | GET | Home page with user authentication info and navigation |
| `/search` | GET/POST | Search the Discogs database for artists, releases, labels, etc. |
| `/artist/{id}` | GET | Display detailed artist information and releases |
| `/release/{id}` | GET | Display detailed release information including tracklist |
| `/collection` | GET | View the authenticated user's Discogs collection |
| `/wantlist` | GET | View the authenticated user's Discogs wantlist |
| `/profile` | GET | View the authenticated user's complete Discogs profile |
| `/random` | GET | Discover random releases from popular artists |

### Route Examples

- **Artist Details**: `/artist/8760` (Pink Floyd)
- **Release Details**: `/release/1` (First release in Discogs database)
- **Search Examples**:
    - `/search?q=Pink Floyd&type=artist`
    - `/search?q=Abbey Road&type=release`
    - `/search?q=Blue Note&type=label`

### API Features Demonstrated

Each route demonstrates different aspects of the Discogs API:

#### Search (`/search`)
- Database search with filtering by type
- Pagination support
- Multiple search types (artist, release, label, master)

#### Artist Details (`/artist/{id}`)
- `getArtist()` - Basic artist information
- `getArtistReleases()` - Artist's discography
- Artist images, aliases, and band members

#### Release Details (`/release/{id}`)
- `getRelease()` - Complete release information
- Tracklist with track details
- Credits and contributors
- Label information and identifiers

#### User Features (`/collection`, `/wantlist`, `/profile`)
- `getOAuthIdentity()` - OAuth user authentication
- `getCollectionFolders()` - Collection organization
- `getCollectionItemsByFolder()` - Collection contents
- `getWantlist()` - User's wanted items
- `getProfile()` - Complete user profile with statistics

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

## Technical Implementation

### Modern PHP Features
- **PHP 8 Attributes** for routing (instead of annotations)
- **Strong typing** with proper return types
- **Exception handling** with user-friendly error messages

### UI/UX Features
- **Bootstrap 5** for responsive design
- **Interactive cards** with hover effects
- **Breadcrumb navigation** for better user experience
- **Flash messages** for user feedback
- **Pagination** for large result sets

### API Integration
- **OAuth authentication** flow
- **Rate limiting** awareness
- **Error handling** for API failures
- **Caching-friendly** implementation

## Code Examples

### Basic Artist Information
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

### Search Implementation
```php
#[Route('/search', name: 'search', methods: ['GET', 'POST'])]
public function search(DiscogsClient $discogs, Request $request): Response
{
    $searchParams = ['q' => $searchQuery, 'per_page' => 20];
    if ($searchType !== 'all') {
        $searchParams['type'] = $searchType;
    }
    
    $searchResults = $discogs->search($searchParams);
    
    return $this->render('search.html.twig', [
        'searchResults' => $searchResults,
        'searchQuery' => $searchQuery,
        'searchType' => $searchType
    ]);
}
```

## Documentation

Further documentation can be found at:
- [Discogs API v2.0 Documentation](https://www.discogs.com/developers)
- [calliostro/discogs-bundle](https://github.com/calliostro/discogs-bundle)
- [calliostro/php-discogs-api](https://github.com/calliostro/php-discogs-api)
