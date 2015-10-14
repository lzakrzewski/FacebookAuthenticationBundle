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

Supported Facebook API version
--------
- v2.5

Installation
--------
#### Step 1: Integrate FOSUserBundle with your app

[Read the Documentation for master](https://symfony.com/doc/master/bundles/FOSUserBundle/index.html).

#### Step 2: Require the FacebookAuthenticationBundle with composer

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
        new Lucaszz\FacebookAuthenticationBundle\LucaszzFacebookAuthenticationBundle(),
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
     * @ORM\Column(type="bigint", nullable=true)
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

**Notice** field for store FacebookId should be named `facebookId` or Annotation `FacebookId` should be used: [FacebookId Annotation](Resources/doc/annotation_facebook_id.md).
 
#### Step 5: Configure the `config.yml`
###### Minimal configuration:

```yaml
lucaszz_facebook_authentication:
    app_id: 1234
    app_secret: secret
```
Parameters: `app_id` and `secret` are needed to get access token: [Access Tokens](https://developers.facebook.com/docs/facebook-login/access-tokens/v2.5).

###### Example of full configuration:
```yaml
lucaszz_facebook_authentication:
    app_id: 1234
    app_secret: secret
    scope: ["public_profile", "email", "user_birthday"]
    fields: ["name", "email", "birthday"]
```

Parameters: 

- `scope` An array of permissions: [Permissions with Facebook Login](https://developers.facebook.com/docs/facebook-login/permissions/v2.5),
- `fields` By default, not all the fields in a node or edge are returned when you make a query. You can choose the fields (or edges) you want returned with the "fields" query parameter. [Choosing Fields](https://developers.facebook.com/docs/graph-api/using-graph-api/v2.5#fields).

**Notice** 

- `scope` Should contain `public_profile`, `email` or more,
- `fields` Should contain `name`, `email` or more.

#### Step 6: Confgure your `routing.yml`

```yaml
facebook_login_path:
    pattern: /facebook/login
```

#### Step 7: Enable `facebook_listener` in your `security.yml`

```yaml
# app/config/security.yml
security:
    # ...
    
    firewalls:
        main:
            pattern: ^/
            form_login:
                provider: fos_userbundle
                csrf_provider: security.csrf.token_manager # Use form.csrf_provider instead for Symfony <2.4

            logout:       true
            anonymous:    true
            # Enable facebook_listener  
            lucaszz_facebook: true
            
    # ...
```

#### Step 8: Update your database schema

```sh
php app/console doctrine:schema:update --force
```

#### Step 9: Setup your facebook app
[Documentation](https://developers.facebook.com/docs)

Now when route `/facebook/login` will be requested then procedure of code exchange will be process [Code exchange](https://developers.facebook.com/docs/facebook-login/access-tokens/v2.5#authNative).

Further documentation
--------
- [FacebookId](Resources/doc/annotation_facebook_id.md)
- [Events](Resources/doc/events.md)
- [Custom login path](Resources/doc/facebook_login_path.md)
- [Testing](Resources/doc/testing.md)