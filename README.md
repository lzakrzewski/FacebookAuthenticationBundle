FacebookAuthenticationBundle
======

[![Build Status](https://travis-ci.org/Lucaszz/FacebookAuthenticationBundle.svg)](https://travis-ci.org/Lucaszz/FacebookAuthenticationBundle) [![Latest Stable Version](https://poser.pugx.org/lucaszz/facebook-authentication-bundle/v/stable)](https://packagist.org/packages/lucaszz/facebook-authentication-bundle) [![Total Downloads](https://poser.pugx.org/lucaszz/facebook-authentication-bundle/downloads)](https://packagist.org/packages/lucaszz/facebook-authentication-bundle)

This bundle provides Facebook authentication for your Symfony2 app using the FOSUserBundle.
Target: Keep it minimalistic and use existing components from Symfony2 and FOSUserBundle.

Features
--------

- Enable login with facebook feature to your app,
- Add user created from facebook data to your app.

Requirements
--------

```json
    "require": {
        "php": ">=5.4",
        "friendsofsymfony/user-bundle": "~2.0@dev",
        "lucaszz/facebook-authentication-adapter": "~1.0"
    }
```

Installation
--------
#### Step 1: Integrate FOSUserBundle with your app:

[Read the Documentation for master](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html).

#### Step 2: Require the FacebookAuthenticationBundle with composer:

```sh
composer require lucaszz/facebook-authentication-bundle "~1.0"
```
#### Step 3: Enable the FacebookAuthenticationBundle

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new \Lucaszz\FacebookAuthenticationBundle\LucaszzFacebookAuthenticationBundle(),
        // ...
    );
}
```
#### Step 4: Implement your User class with FacebookUser

```php
<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser;

/**
 * @ORM\Entity
 * @ORM\Table(name="users")
 */
class User extends BaseUser implements FacebookUser
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\Column(type="bigint")
     */
    private $facebookId;

    /** {@inheritdoc} */
    public function getFacebookId()
    {
        return $this->facebookId;
    }

    /** {@inheritdoc} */
    public function setFacebookId($facebookId)
    {
        $this->facebookId = $facebookId;
    }
}

```

**Notice** field for store Facebook Id should be named `facebookId` or Annotation `FacebookId` should be used. [FacebookId](Resources/doc/facebook_id.md)
 
#### Step 5: Configure the `config.yml`

```yaml
lucaszz_facebook_authentication:
    app_id: 1234
    app_secret: secret
    scope: ["public_profile", "email"]
```

Parameters: `app_id` and `secret` are needed to get access token: [Access Tokens](https://developers.facebook.com/docs/facebook-login/access-tokens)

**Notice** `scope` should contain `public_profile`, `email` or more: [Permissions with Facebook Login](https://developers.facebook.com/docs/facebook-login/permissions).

#### Step 6: Confgure your `routing.yml`

```yaml
facebook_login_path:
    pattern: /facebook/login
```

#### Step 7: Update your database schema

```sh
php app/console doctrine:schema:update --force
```

#### Step 8: Setup your facebook app:
Todo: create documentation of this

Now when route `/facebook/login` will be requested then procedure of code exchange will be process [Code exchange](https://developers.facebook.com/docs/facebook-login/access-tokens#authNative)

Further documentation
--------
- [FacebookId](Resources/doc/facebook_id.md)
- [Events](Resources/doc/events.md)