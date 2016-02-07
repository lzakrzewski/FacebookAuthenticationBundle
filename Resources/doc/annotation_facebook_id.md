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
use Lzakrzewski\FacebookAuthenticationBundle\Model\FacebookUser;
use Lzakrzewski\FacebookAuthenticationBundle\Annotation as Lzakrzewski;

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
     * @Lzakrzewski\FacebookId
     * @ORM\Column(type="bigint", nullable=true)
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