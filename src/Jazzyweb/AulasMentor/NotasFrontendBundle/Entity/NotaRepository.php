<?php

namespace Jazzyweb\AulasMentor\NotasFrontendBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * NotaRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NotaRepository extends EntityRepository {

    /**
     * 
     * @param Tema $tema 
     * @param array $tags an array of tags id's
     * @return array an array of the user notes which have all the tags given by $tags
     * @throws \Exception
     */
    public function findByTemaAndEtiqueta($tema, $tags) {
//        print_r($tags);exit;
        if (!is_array($tags)) {
            throw new \Exception("tags must be an array of tags id's");
        }

        if (count($tags) == 0) {
            return;
        }
//        print_r($tags);exit;        
        $inTags = '';
        $i = 0;
        $numOfTags = count($tags);
        foreach ($tags as $t) {
            $inTags .= $t;
            $i++;
            if ($i != $numOfTags)
                $inTags.=',';
        }

//        print_r($inTags);exit;
        $query = $this->getEntityManager()->createQuery(
                        "SELECT n FROM JAMNotasFrontendBundle:Nota n
                         JOIN  n.etiquetas e
                         JOIN n.tema t
                         WHERE e.id in (" . $inTags . ") and t=:tema")
                ->setParameters(array('tema' => $tema));


        $notes = $query->getResult();

        $notes = $this->notesIntersection($tags, $notes);

        return $notes;
    }

    public function findByUsuarioOrderedByFecha($usuario) {
        $query = $this->getEntityManager()->createQuery(
                        "SELECT n FROM JAMNotasFrontendBundle:Nota n
                         JOIN n.usuario u
                          WHERE  u.username=:username ORDER BY n.fecha DESC")
                ->setParameters(array('username' => $usuario->getUsername()));

        return $query->getResult();
    }

    public function findByUsuarioAndTermino($usuario, $termino) {
        if ($usuario instanceof Usuario) {
            $username = $usuario->getUsername();
        } else {
            $username = $usuario;
        }

        $query = $this->getEntityManager()->createQuery(
                        "SELECT n FROM JAMNotasFrontendBundle:Nota n
                         JOIN n.usuario u
                         WHERE u.username=:username AND (n.texto LIKE :termino OR n.titulo LIKE :termino)")
                ->setParameters(array('username' => $username, 'termino' => '%' . $termino . '%'));

        return $query->getResult();
    }

    /**
     * 
     * @param array $tags an array with the tag id's
     * @param array $notes an array with notes
     * @return array the notes which have all the tag id's
     */
    protected function notesIntersection($tags, $notes) {
        foreach ($notes as $k => $n) {
            $tagsNote = $n->getEtiquetas();

            $tag_ids = array();
            foreach ($tagsNote as $t) {
                $tag_ids[] = $t->getId();
            }

            $intersection = array_values(array_intersect($tag_ids, $tags));
            sort($intersection);
            sort($tags);

            if ($intersection != $tags) {
                unset($notes[$k]);
            }
        }
       
        // It's needed that the first element of the array have 0 as key
               
        return array_values($notes);
    }

}