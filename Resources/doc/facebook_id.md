FacebookId
--------

`FacebookId` is annotation to mark field in User class used to find User in database.

## Example:

```php
<?php
// src/AppBundle/Entity/User.php

namespace AppBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;
use Lucaszz\FacebookAuthenticationBundle\Model\FacebookUser;
use Lucaszz\FacebookAuthenticationBundle\Annotation as Lucaszz;

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
     * @Lucaszz\FacebookId
     * @ORM\Column(type="bigint")
     */
    private $apiId;

    /** {@inheritdoc} */
    public function getFacebookId()
    {
        return $this->apiId;
    }

    /** {@inheritdoc} */
    public function setFacebookId($apiId)
    {
        $this->apiId = $apiId;
    }
}
```