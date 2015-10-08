<!DOCTYPE html>
<html>
    <body>
        <form action="<?php echo $view['router']->generate("fos_user_security_check") ?>" method="post">
            <input type="hidden" name="_csrf_token" value="<?php echo $csrf_token ?>"/>

            <label for="username">Username</label>
            <input type="text" id="username" name="_username" value="<?php echo $last_username ?>" required="required"/>

            <label for="password">Password</label>
            <input type="password" id="password" name="_password" required="required"/>

            <input type="checkbox" id="remember_me" name="_remember_me" value="on"/>
            <label for="remember_me">Remember me</label>

            <input type="submit" id="_submit" name="_submit" value="Submit"/>
        </form>
    </body>
</html>