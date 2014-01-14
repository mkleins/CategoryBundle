<?php

namespace Wd\TreeBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller,
    Symfony\Component\HttpFoundation\Response;
use Wd\TreeBundle\Interfaces\TreeControllerEditableInterface;

class DefaultController extends Controller implements TreeControllerEditableInterface
{
    public function addNodeAction($parent, $title)
    {
       $em = $this->getDoctrine()->getManager();
       $repo = $em->getRepository('WdTreeBundle:Category');
       $parent = $repo->find($parent);
       $child = new Referential();
       $child->setName($title);
       $child->setParent($parent);
       $em->persist($child);
       $em->flush();
       return new JsonResponse(array('status'=>true,'id'=>$child->getId()));
    }

    public function removeNodeAction($node)
    {
       $em = $this->getDoctrine()->getManager();
       $repo = $em->getRepository('WdTreeBundle:Category');
       $node = $repo->find($node);
       $em->remove($node);
       $em->flush();
       return new Response('ok');
    }

    public function renameNodeAction($node, $title)
    {
       $em = $this->getDoctrine()->getManager();
       $repo = $em->getRepository('WdTreeBundle:Category');
       $node = $repo->find($node);
       $node->setName($title);
       $em->persist($node);
       $em->flush();
       return new JsonResponse(array('status'=>true));
    }

    function sortNodeAction($node, $ref, $move)
    {
       try {
            $em = $this->getDoctrine()->getManager();
            $repo = $em->getRepository('WdTreeBundle:Category');
            $moved = $repo->findOneBy(array('id' => $node));
            $reference = $repo->findOneBy(array('id' => $ref));
            if ($move=='before') {
                $repo->persistAsPrevSiblingOf($moved, $reference);
            } else if($move=='after'){
                $repo->persistAsNextSiblingOf($moved, $reference);
            } else{
                $repo->persistAsFirstChildOf($moved, $reference);
            }
            $em->flush();
            return new Response('ok');
        } catch (\Exception $e) {
            return new Response('ko');
        }
    }
}
