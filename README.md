[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/matecat/emoji-to-entity-converter/badges/quality-score.png?b=main)](https://scrutinizer-ci.com/g/matecat/emoji-to-entity-converter/?branch=main)
[![Build Status](https://app.travis-ci.com/matecat/emoji-to-entity-converter.svg?token=qBazxkHwP18h3EWnHjjF&branch=main)](https://app.travis-ci.com/matecat/emoji-to-entity-converter)

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

## Support

If you found an issue or had an idea please refer [to this section](https://github.com/matecat/emoji-to-entity-converter/issues).

## Authors

* **Domenico Lupinetti** - [github](https://github.com/ostico)
* **Mauro Cassani** - [github](https://github.com/mauretto78)

## License

This project is licensed under the MIT License - see the [LICENSE.md](LICENSE.md) file for details