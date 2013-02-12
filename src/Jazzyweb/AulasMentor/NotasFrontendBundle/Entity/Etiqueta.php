<?php

namespace Jazzyweb\AulasMentor\NotasFrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Etiqueta
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\EtiquetaRepository")
 */
class Etiqueta {

    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $texto
     *
     * @ORM\Column(name="texto", type="string", length=255)
     */
    private $texto;

    ////ASOCIACIONES////

    /**
     * @ORM\ManyToMany(targetEntity="Nota", mappedBy="etiquetas")
     */
    private $notas;

    /**
     * @ORM\ManyToMany(targetEntity="Usuario", mappedBy="etiquetas")
     */
    private $usuarios;

    ////FIN ASOCIACIONES////

    public function __construct() {
        $this->notas = new ArrayCollection();
        $this->etiquetas = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set texto
     *
     * @param string $texto
     */
    public function setTexto($texto) {
        $this->texto = $texto;
    }

    /**
     * Get texto
     *
     * @return string 
     */
    public function getTexto() {
        return $this->texto;
    }

    /**
     * Add notas
     *
     * @param Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas
     */
    public function addNota(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas) {
        $this->notas[] = $notas;
    }

    /**
     * Get notas
     *
     * @return Doctrine\Common\Collections\Collection 
     */
    public function getNotas() {
        return $this->notas;
    }

    /**
     * Set usuario
     *
     * @param Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuario
     */
    public function setUsuario(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuario) {
        $this->usuario = $usuario;
    }

    /**
     * Get usuario
     *
     * @return Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario 
     */
    public function getUsuario() {
        return $this->usuario;
    }

    /**
     * Remove notas
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas
     */
    public function removeNota(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas) {
        $this->notas->removeElement($notas);
    }

    public function getNumeroDeNotasDelUsuario($usuario) {

        if (!$usuario instanceof Usuario)
            throw new \Exception("El parámetro pasado no es un objeto del tipo Usuario");

        $notas = $this->getNotas();
        foreach ($notas as $n) {
            if ($n->getUsuario() != $usuario)
                $notas->removeElement($n);
        }

        return count($this->getNotas());
    }


    /**
     * Add usuario
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuario
     * @return Etiqueta
     */
    public function addUsuario(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuario)
    {
        $this->usuario[] = $usuario;
    
        return $this;
    }

    /**
     * Remove usuario
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuario
     */
    public function removeUsuario(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuario)
    {
        $this->usuario->removeElement($usuario);
    }

    /**
     * Get usuarios
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsuarios()
    {
        return $this->usuarios;
    }
}