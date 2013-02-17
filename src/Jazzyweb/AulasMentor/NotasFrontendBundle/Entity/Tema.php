<?php

namespace Jazzyweb\AulasMentor\NotasFrontendBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Tema
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\TemaRepository")
 */
class Tema
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nombre", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    private $nombre;

    /**
     * @var string
     *
     * @ORM\Column(name="descripcion", type="string", length=255)
     * 
     * @Assert\NotBlank()
     * @Assert\MaxLength(255)
     */
    private $descripcion;

    /**
     * @ORM\ManyToOne(targetEntity="Usuario", inversedBy="misTemas")
     **/
    private $propietario;
    
     /** 
     * @ORM\ManyToMany(targetEntity="Usuario", inversedBy="temasCompartidos")
     */
    private $usuarios;
    
    /**
     *  @ORM\OneToMany(targetEntity="Nota", mappedBy="tema")
     */
    private $notas;
    
    /**
     * @ORM\ManyToMany(targetEntity="Etiqueta", inversedBy="temas")
     */
    private $etiquetas;

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set nombre
     *
     * @param string $nombre
     * @return Tema
     */
    public function setNombre($nombre)
    {
        $this->nombre = $nombre;
    
        return $this;
    }

    /**
     * Get nombre
     *
     * @return string 
     */
    public function getNombre()
    {
        return $this->nombre;
    }

    /**
     * Set descripcion
     *
     * @param string $descripcion
     * @return Tema
     */
    public function setDescripcion($descripcion)
    {
        $this->descripcion = $descripcion;
    
        return $this;
    }

    /**
     * Get descripcion
     *
     * @return string 
     */
    public function getDescripcion()
    {
        return $this->descripcion;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->usuarios = new \Doctrine\Common\Collections\ArrayCollection();
    }
    
    /**
     * Set propietario
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $propietario
     * @return Tema
     */
    public function setPropietario(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $propietario = null)
    {
        $this->propietario = $propietario;
    
        return $this;
    }

    /**
     * Get propietario
     *
     * @return \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario 
     */
    public function getPropietario()
    {
        return $this->propietario;
    }

    /**
     * Add usuarios
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuarios
     * @return Tema
     */
    public function addUsuario(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios[] = $usuarios;
    
        return $this;
    }

    /**
     * Remove usuarios
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuarios
     */
    public function removeUsuario(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Usuario $usuarios)
    {
        $this->usuarios->removeElement($usuarios);
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

    /**
     * Add notas
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas
     * @return Tema
     */
    public function addNota(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas)
    {
        $this->notas[] = $notas;
    
        return $this;
    }

    /**
     * Remove notas
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas
     */
    public function removeNota(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Nota $notas)
    {
        $this->notas->removeElement($notas);
    }

    /**
     * Get notas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getNotas()
    {
        return $this->notas;
    }

    /**
     * Add etiquetas
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Etiqueta $etiquetas
     * @return Tema
     */
    public function addEtiqueta(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Etiqueta $etiquetas)
    {
        $this->etiquetas[] = $etiquetas;
    
        return $this;
    }

    /**
     * Remove etiquetas
     *
     * @param \Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Etiqueta $etiquetas
     */
    public function removeEtiqueta(\Jazzyweb\AulasMentor\NotasFrontendBundle\Entity\Etiqueta $etiquetas)
    {
        $this->etiquetas->removeElement($etiquetas);
    }

    /**
     * Get etiquetas
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getEtiquetas()
    {
        return $this->etiquetas;
    }
}