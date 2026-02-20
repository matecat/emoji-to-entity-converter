[![Build Status](https://app.travis-ci.com/matecat/emoji-to-entity-converter.svg?token=qBazxkHwP18h3EWnHjjF&branch=main)](https://app.travis-ci.com/matecat/emoji-to-entity-converter)
[![Quality Gate Status](https://sonarcloud.io/api/project_badges/measure?project=matecat_emoji-to-entity-converter&metric=alert_status)](https://sonarcloud.io/summary/new_code?id=matecat_emoji-to-entity-converter)
[![Coverage](https://sonarcloud.io/api/project_badges/measure?project=matecat_emoji-to-entity-converter&metric=coverage)](https://sonarcloud.io/summary/new_code?id=matecat_emoji-to-entity-converter)
[![Reliability Rating](https://sonarcloud.io/api/project_badges/measure?project=matecat_emoji-to-entity-converter&metric=reliability_rating)](https://sonarcloud.io/summary/new_code?id=matecat_emoji-to-entity-converter)
[![Maintainability Rating](https://sonarcloud.io/api/project_badges/measure?project=matecat_emoji-to-entity-converter&metric=sqale_rating)](https://sonarcloud.io/summary/new_code?id=matecat_emoji-to-entity-converter)

# Usage

This library provides two methods to convert emojis in their corresponding Unicode Hexadecimal Code entities.

### Emoji to entity

```php
// This will return &#129767;
Emoji::toEntity("🫧");
```

### Entity to emoji 

```php
// This will return 🪥
Emoji::toEmoji("&#129701;");
```

## Automatic update the emoji map 😀 

An automated tool is provided by the library to update the emoji map.

It is based on the [Open Emoji Map](https://emoji-api.com/) project and all credit goes to its author.

First, you need to install all the dev dependencies. Afterwards, obtain your Open Emoji Map API key, copy the contents of `credentials.dist.ini` file into `credentials.ini` and insert the key.

Subsequently, enter the following command in your terminal:

```cli
php bin/console emoji:update
```

## Support

If you found an issue or had an idea please refer [to this section](https://github.com/matecat/emoji-to-entity-converter/issues).

## Authors

* **Domenico Lupinetti** - [github](https://github.com/ostico)
* **Mauro Cassani** - [github](https://github.com/mauretto78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details