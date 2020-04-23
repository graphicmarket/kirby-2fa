# Kirby 2fa

Two factor authentication for the Kirby panel.

![cover](https://raw.githubusercontent.com/graphicmarket/kirby-2fa/develop/.github/cover%20kiby2fa.png)

## Installation

>Before install, have in mind that the only way to implement this is by replacing the default [panel login view](https://getkirby.com/docs/reference/plugins/extensions/panel-login). Hence if you have your implementation, installing this plugin can be conflictive.

### Download

Download and copy the content of this repository to `/site/plugins/kirby-2fa` directory.

### Composer

```
composer require graphicmarket/kirby-2fa
```

## Setup

1. Add a field in your user blueprint
  ```yaml
    auth:
      type: 2fa
  ```

2. A button will appear on the panel. When you click on it the next modal will be displayed, just follow the steps to configure.

![panel-setup](https://raw.githubusercontent.com/graphicmarket/kirby-2fa/develop/.github/Panel%20setup%201.png)

****

You can add to your config file where you want the auth data will be stored, which must be a SQL lite file. Don't worry about creating the file you only need to specify the path with the filename. the file will be auto-created and configured if it doesn't exist.

>:warning: **This option is strongly recommended, save the file on a secure directory, don't push it to a repository.**


```php
  'graphicmarket.kirby-2fa.database' => 'full/path/to/kirby-2fa/db.sqlite'
```

Too, you can use a function that returns a string, if you wish to use the `kirby()` helper.

```php
  'graphicmarket.kirby-2fa.database' => function () {
      return kirby()->root('storage') . '/kirby-2fa/db.sqlite';
  },
```

Change the issuer sounds like something that may you want to make. The issuer is the identifier that will be displayed on the authenticator app. By default is kirby-2fa panel.

![issuer_example](https://raw.githubusercontent.com/graphicmarket/kirby-2fa/develop/.github/issuer%20exaple.png)

```php
  'graphicmarket.kirby-2fa.issuer' => 'Your company/website name',
```

## Recommendation

Set in your auth options a lower number of trials by following the Kirby [panel security options](https://getkirby.com/docs/reference/system/options/auth). five its ok for me.

## Improvements and future features

1. Passwordless login (Email/SMS).
2. Implements own QRProvider.
3. Save data in the user's DB.
4. Choose the caching driver storge.
5. Translation files.

## License

MIT

## Disclaimer

This plugin is provided "as is" with no guarantee. Use it at your own risk and always test it yourself before using it in a production environment. If you find any issues, please [create a new issue](https://github.com/graphicmarket/kirby-2fa/issues/new/choose).

## Credits

- [Ronald Torres](https://github.com/rtorresn10)
