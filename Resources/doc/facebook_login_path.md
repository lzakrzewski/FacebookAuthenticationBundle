Custom path for FacebookListener
--------

FacebookListener is called in every request.
If pattern of requested route matches `facebook_login_path` parameter then FacebookListener works and executes procedure for login with facebook.

It could be customized.

## Example: How to set custom path for FacebookListener

#### Step 1: Add `facebook_login_path` to `security.yml`
```yaml
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
            # set custom path 
            lucaszz_facebook:
                facebook_login_path: /custom-facebook-login

    # ...
    
```

#### Step 2: Add route to `routing.yml`
```yaml
#...

facebook_login_path:
    pattern: /custom-facebook-login

# ...
```