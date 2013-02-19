<?php

namespace Jazzyweb\AulasMentor\NotasFrontendBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota;
use Jazzyweb\AulasMentor\NotasFrontendBundle\Form\Type\NotaType;
use Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Etiqueta;
use Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Tema;

class NotasController extends Controller {

    public function indexAction() {
        $request = $this->getRequest(); // equivalente a $this->get('request');
        $session = $this->get('session');

        $ruta = $request->get('_route');

        switch ($ruta) {
            case 'jamn_homepage':

                break;

            case 'jamn_conetiqueta':
                $session->set('search.type', 'by_tags');
                $session->set('search.value', $request->get('etiqueta'));
                $session->set('nota.seleccionada.id', '');

                break;

            case 'jamn_addtag':
                $tags = $session->get('search.value');
                if (!is_array($tags)) {
                    $tags = array();
                }
                $tags[] = $request->get('tag');
                $tags = array_unique($tags);
                $session->set('search.type', 'by_tags');
                $session->set('search.value', $tags);
                $session->set('nota.seleccionada.id', '');

                break;

            case 'jamn_removetag':
                $tags = $session->get('search.value');
                $tag = $request->get('tag');
                $tags = array_diff($tags, array($tag));

                $session->set('search.type', 'by_tags');
                $session->set('search.value', $tags);
                $session->set('nota.seleccionada.id', '');
                //exit;
                break;

            case 'jamn_buscar':
                $session->set('search.type', 'by_term');
                $session->set('search.value', $request->get('termino'));
                $session->set('nota.seleccionada.id', '');

                break;
            case 'jamn_nota':
                $session->set('nota.seleccionada.id', $request->get('id'));
                break;
            case 'changesubject':
                $session->set('search.value', $tags);
                $session->set('nota.seleccionada.id', '');
                $session->set('subject', $request->get('subject'));
                break;
        }

        list($temas, $etiquetas, $notas, $notaSeleccionada) = $this->dameTemasEtiquetasYNotas();

        // creamos un formulario para borrar la nota
        if ($notaSeleccionada instanceof Nota) {
            $deleteForm = $this->createDeleteForm($notaSeleccionada->getId())->createView();
        } else {
            $deleteForm = null;
        }

        return $this->render('JAMNotasFrontendBundle:Notas:index.html.twig', array(
                    'temas' => $temas,
                    'etiquetas' => $etiquetas,
                    'notas' => $notas,
                    'nota_seleccionada' => $notaSeleccionada,
                    'delete_form' => $deleteForm,
                ));
    }

    public function nuevaAction() {
        $request = $this->getRequest();

        list($temas, $etiquetas, $notas, $nota_seleccionada) = $this->dameTemasEtiquetasYNotas();

        $em = $this->getDoctrine()->getEntityManager();

        $allEtiquetas = $em->getRepository('JAMNotasFrontendBundle:Etiqueta')->
                findAllOrderedByTexto();
        $nota = new Nota();
        $newForm = $this->createForm(new NotaType(), $nota);

        if ($request->getMethod() == "POST") {

            $newForm->bindRequest($request);

            if ($newForm->isValid()) {
                $usuario = $this->get('security.context')->getToken()->getUser();

                $item = $request->get('item');
                $this->actualizaEtiquetas($nota, $item['tags'], $usuario);

                $nota->setUsuario($usuario);
                $nota->setFecha(new \DateTime());

                if ($newForm['file']->getData() != '')
                    $nota->upload($usuario->getUsername());

                $em->persist($nota);

                $em->flush();

                $session = $this->get('session');
                $session->set('nota.seleccionada.id', $request->get('id'));

                return $this->redirect($this->generateUrl('jamn_homepage'));
            }
        }

        return $this->render('JAMNotasFrontendBundle:Notas:crearOEditar.html.twig', array(
                    'temas' => $temas,
                    'etiquetas' => $etiquetas,
                    'alletiquetas' => $allEtiquetas,
                    'notas' => $notas,
                    'nota_seleccionada' => $nota,
                    'form' => $newForm->createView(),
                    'edita' => false,
                ));
    }

    public function editarAction() {
        $request = $this->getRequest();
        $id = $request->get('id');
        list($temas, $etiquetas, $notas, $nota_seleccionada) = $this->dameTemasEtiquetasYNotas();

        $em = $this->getDoctrine()->getEntityManager();

        $allEtiquetas = $em->getRepository('JAMNotasFrontendBundle:Etiqueta')->
                findAllOrderedByTexto();
        $nota = $em->getRepository('JAMNotasFrontendBundle:Nota')->find($id);

        if (!$nota) {
            throw $this->createNotFoundException('No se ha podido encontrar esa nota');
        }

        $editForm = $this->createForm(new NotaType(), $nota);
        $deleteForm = $this->createDeleteForm($id);

        if ($this->getRequest()->getMethod() == "POST") {

            $editForm->bindRequest($request);

            if ($editForm->isValid()) {
                $usuario = $this->get('security.context')->getToken()->getUser();

                $item = $request->get('item');
                $this->actualizaEtiquetas($nota, $item['tags'], $usuario);

                $nota->setFecha(new \DateTime());

                if ($editForm['file']->getData() != '')
                    $nota->upload($usuario->getUsername());

                $em->persist($nota);

                $em->flush();

                return $this->redirect($this->generateUrl('jamn_homepage'));
            }
        }

        return $this->render('JAMNotasFrontendBundle:Notas:crearOEditar.html.twig', array(
                    'temas' => $temas,
                    'etiquetas' => $etiquetas,
                    'alletiquetas' => $allEtiquetas,
                    'notas' => $notas,
                    'nota_seleccionada' => $nota,
                    'form' => $editForm->createView(),
                    'delete_form' => $deleteForm->createView(),
                    'edita' => true,
                ));
    }

    public function borrarAction() {
        $request = $this->getRequest();
        $session = $this->get('session');
        $form = $this->createDeleteForm($request->get('id'));

        $form->bindRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getEntityManager();
            $entity = $em->getRepository('JAMNotasFrontendBundle:Nota')->find($request->get('id'));

            if (!$entity) {
                throw $this->createNotFoundException('Esa nota no existe.');
            }

            $em->remove($entity);
            $em->flush();

            $session->set('nota.seleccionada.id', '');
        }

        return $this->redirect($this->generateUrl('jamn_homepage'));
    }

    public function miEspacioAction() {
        $params = 'Los datos de la página de inicio del espacio premium';
        return $this->render('JAMNotasFrontendBundle:Notas:index', array('params' => $params));
    }

    public function rssAction() {
        
    }

    protected function dameTemasEtiquetasYNotas() {
        $session = $this->get('session');
        $em = $this->getDoctrine()->getEntityManager();

        $usuario = $this->get('security.context')->getToken()->getUser();

        $busqueda_tipo = $session->get('search.type');

        $busqueda_valor = $session->get('search.value');
        

        // print_r($busqueda_valor);exit;
        // Temas. Pillamos todos los temas del usuario
        // 
        $temas = $em->getRepository('JAMNotasFrontendBundle:Tema')->
                findByUsuarioOrderedByNombre($usuario);
        // Etiquetas. Se pillan todas las etiquetas en las que el usuario tiene
        // alguna nota
        
        if ($session->has('subject')) {
            $temaId = $session->get('subject');
        }else if(count($temas)>0 && $temas[0] instanceof Tema){
            $temaId = $temas[0]->getId();
        }else{
            $temaId = 0;
        }
        $tema = $em->getRepository('JAMNotasFrontendBundle:Tema')->find($temaId);
        $etiquetas = $em->getRepository('JAMNotasFrontendBundle:Etiqueta')->
                findByTemaOrderedByTexto($tema);

        // Notas. Se pillan según el filtro almacenado en la sesión
        if ($busqueda_tipo == 'by_tags' && $busqueda_valor != 'todas') {
            $notas = $em->getRepository('JAMNotasFrontendBundle:Nota')->
                    findByTemaAndEtiqueta($tema, $busqueda_valor);
        } elseif ($busqueda_tipo == 'by_term') {
            $notas = $em->getRepository('JAMNotasFrontendBundle:Nota')->
                    findByUsuarioAndTermino($usuario, $busqueda_valor);
        } else {
            $notas = $em->getRepository('JAMNotasFrontendBundle:Nota')->
                    findByUsuarioOrderedByFecha($usuario);
        }

        $nota_seleccionada = null;
        if (count($notas) > 0) {
            $nota_selecionada_id = $session->get('nota.seleccionada.id');
            if (!is_null($nota_selecionada_id) && $nota_selecionada_id != '') {
                $nota_seleccionada = $em->getRepository('JAMNotasFrontendBundle:Nota')->
                        findOneById($nota_selecionada_id);
            } else {
                $nota_seleccionada = $notas[0];
            }
            $nota_seleccionada->setSelected(true);
        }

        return array($temas, $etiquetas, $notas, $nota_seleccionada);
    }

    protected function createDeleteForm($id) {
        return $this->createFormBuilder(array('id' => $id))
                        ->add('id', 'hidden')
                        ->getForm()
        ;
    }

    protected function actualizaEtiquetas($nota, $tags, $usuario) {

        if (count($tags) == 0) {
            $tags = array();
        }
        $em = $this->getDoctrine()->getEntityManager();

        $nota->getEtiquetas()->clear();

        foreach ($tags as $tag) {
            //$etiqueta = $em->getRepository('JAMNotasFrontendBundle:Etiqueta')->findOneByTextoAndUsuario($tag, $usuario);
            $etiqueta = $em->getRepository('JAMNotasFrontendBundle:Etiqueta')->findOneByTexto($tag);
            if (!$etiqueta instanceof Etiqueta) {
                $etiqueta = new Etiqueta();
                $etiqueta->setTexto($tag);
                $em->persist($etiqueta);
            }
            if (!$usuario->hasEtiqueta($etiqueta)) {
                $usuario->addEtiqueta($etiqueta);
            }
            $nota->addEtiqueta($etiqueta);
        }

        $em->flush();
    }

}

