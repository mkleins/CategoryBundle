WdCategoryBundle
Bundle
=================

A Symfony2 bundle to manage those tree structures!

This is a new version of the symfony 2.x bundle: [CypressTreeBundle](https://github.com/matteosister/CypressTreeBundle) of Matteosister.

Depends on
----------

[FOSJsRoutingBundle](https://github.com/FriendsOfSymfony/FOSJsRoutingBundle)

Install
-------

add this to the *require* section of your **composer.json** file

```javascript
{
    "require": {
        "wd/tree-bundle": "dev-master"
    }
}
```

Activate both the **CypressTreeBundle** and the **FOSJsRoutingBundle** in you AppKernel class

*app/AppKernel.php*
```php
<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        // other bundles...

        $bundles[] = new Wd\TreeBundle\CypressTreeBundle();
        $bundles[] = new FOS\JsRoutingBundle\FOSJsRoutingBundle();

        return $bundles;
    }
}
```

Configuration
-------------

You have a php class (an entity, a document, or anything else) and you want it to be a part of a tree structure.
For this example let's consider a Doctrine ORM entity with the Tree extension enabled from the awesome [gedmo repository](https://github.com/l3pp4rd/DoctrineExtensions). Here is a [full configuration tutorial](http://gediminasm.org/article/tree-nestedset-behavior-extension-for-doctrine-2)

* Add the TreeInterface to the class

```php
<?php
namespace Wd\MyBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use Wd\TreeBundle\Interfaces\TreeInterface;

/**
 * MenuItem Document
 *
 * @ORM\Entity(repositoryClass="Vivacom\CmsBundle\Repositories\ORM\MenuItemRepository")
 * @Gedmo\Tree(type="nested")
 */
class MenuItem implements TreeInterface {
    /**
     * @var MenuItem
     *
     * @ORM\ManyToOne(targetEntity="MenuItem", inversedBy="children")
     * @ORM\JoinColumn(onDelete="CASCADE")
     * @Gedmo\TreeParent
     */
    private $parent;

    /**
     * var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="MenuItem", mappedBy="parent", cascade={"all"})
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    private $children;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * add a MenuItem as children
     *
     * @param MenuItem $menuItem the children page
     */
    public function addChildren(MenuItem $menuItem)
    {
        $menuItem->setParent($this);
        $this->children[] = $menuItem;
    }

    /**
     * Children setter
     *
     * @param \Doctrine\Common\Collections\ArrayCollection $children la variabile children
     */
    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * Children getter
     *
     * @return \Doctrine\Common\Collections\ArrayCollection
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Parent setter
     *
     * @param \Vivacom\CmsBundle\Document\MenuItem $parent the parent property
     */
    public function setParent($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Parent getter
     *
     * @return \Vivacom\CmsBundle\Document\MenuItem
     */
    public function getParent()
    {
        return $this->parent;
    }
}
```

**TreeInterface** define one method that you MUST implement: **getChildren()**

```php
<?php
namespace Cypress\TreeBundle\Interfaces;

/**
 * Interface for trees
 */
interface TreeInterface
{
    /**
     * returns a in iteratable object of children
     *
     * @abstract
     * @return mixed
     */
    function getChildren();
}
```

We already have it in the class as a getter for the *children* property.
You could use any class, as far as you implement TreeInterface, and define the *getChildren* method.

Now define your trees in the config.yml file. Here is a complete reference:

*app/config/config.yml*
```YAML
cypress_tree:
    trees:
        my_tree:
            label_template: "MyAwesomeBundle:Menu:item_label.html.twig"
            controller: "MyAwesomeBundle:Menu"
            editable_root: false
            theme: default # or default-rl, classic, apple
            root_icon: 'bundles/cypresstree/images/database.png'
            node_icon: 'bundles/cypresstree/images/folder.png'
```

Under the *trees* section you define every tree that you need for your application. Pick a name that means something to you.
For every tree you define:
- **label_template**: A twig template that gets a *node* variable with the object of the current tree

*src/MyAwesomeBundle/Resources/views/Menu/item_label.html.twig*
```HTML+Django
<a href="{{ path('admin_menuitems_edit_mongodb', { 'id': node.id }) }}">{{ node }}</a>
```

- **controller**: Use the short notation to define a Controller to handle the tree sorting actions.
The controller should implements the *TreeControllerSortableInterface* which requires you to define the *function sortNodeAction($node, $ref, $move);* method
Here is an example with Doctrine orm entities

```php
<?php
namespace Cypress\MyAwesomeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Cypress\TreeBundle\Interfaces\TreeControllerSortableInterface;

class AdminController extends Controller implements TreeControllerSortableInterface
    public function sortNodeAction($node, $ref, $move)
    {
        try {
            $repo = $this->get('doctrine.orm.entity_manager')->getRepository('Cypress\MyAwesomeBundle\Entity\MenuItem');
            $moved = $repo->findOneBy(array('id' => $node));
            $reference = $repo->findOneBy(array('id' => $ref));
            $moveAfter = $move == 1;
            if (!$moveAfter) {
                $repo->persistAsPrevSiblingOf($moved, $reference);
            } else {
                $repo->persistAsNextSiblingOf($moved, $reference);
            }
            $this->getEM()->flush();
            return new Response('ok');
        } catch (\Exception $e) {
            return new Response('ko');
        }
    }

    // other actions...
}
