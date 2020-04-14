# Kirby 2fa
*Add description and cover*
## Installation

Before install, have in mind that the only way to implement this is by replacing the default [panel login view](https://getkirby.com/docs/reference/plugins/extensions/panel-login). Hence if you have your implementation, install this plugin can be conflictive.

### Download

Download and copy this repository to `/site/plugins/` path.

### Composer

```
composer require rtorresn10/kirby-2fa
```

## Setup

1. Add a field in your user blueprint
  ```
    auth:
      type: 2fa
  ```

2. In the panel, follow the steps.

*Upload a image example*

****

You can add to your config file where you want the auth data will be stored, which must be a SQL lite file. Don't worry about creating the file you only need to specify the path. the file will be auto-created and configured if it doesn't exist.

```
  'graphicmarket.kirby-2fa.database' => 'full/path/to/db.sqlite'
```

Too, you can use a function that returns a string, if you wish to use the `kirby()` helper.

```
  'graphicmarket.kirby-2fa.database' => function () {
      return kirby()->root('storage') . '/kirby-2fa/db.sqlite';
  },
```

Change the issuer sounds like something that may you want to change. The issuer is the identifier that will be displayed on the authentication app.

*Upload image example*

```
  'graphicmarket.kirby-2fa.issuer' => 'Your company/website name',
```


## Improvements and Features

1. Passwordless login
2. Implements own QRProvider.

## License

MIT

## Credits

- [Ronald Torres](https://github.com/rtorresn10)
