<?php

class Anuncio {
    private $id;
    private $idusuario;
    private $nombre;
    private $descripcion;
    private $precio;
    private $fechacreacion;
    private $finalizado;

    /**
     * Get the value of id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Set the value of id
     */
    public function setId($id): self {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of idusuario
     */
    public function getIdusuario() {
        return $this->idusuario;
    }

    /**
     * Set the value of idusuario
     */
    public function setIdusuario($idusuario): self {
        $this->idusuario = $idusuario;
        return $this;
    }

    /**
     * Get the value of nombre
     */
    public function getNombre() {
        return $this->nombre;
    }

    /**
     * Set the value of nombre
     */
    public function setNombre($nombre): self {
        $this->nombre = $nombre;
        return $this;
    }

    /**
     * Get the value of descripcion
     */
    public function getDescripcion() {
        return $this->descripcion;
    }

    /**
     * Set the value of descripcion
     */
    public function setDescripcion($descripcion): self {
        $this->descripcion = $descripcion;
        return $this;
    }

    /**
     * Get the value of precio
     */
    public function getPrecio() {
        return $this->precio;
    }

    /**
     * Set the value of precio
     */
    public function setPrecio($precio): self {
        $this->precio = $precio;
        return $this;
    }

    /**
     * Get the value of fechacreacion
     */
    public function getFechacreacion() {
        return $this->fechacreacion;
    }

    /**
     * Set the value of fechacreacion
     */
    public function setFechacreacion($fechacreacion): self {
        $this->fechacreacion = $fechacreacion;
        return $this;
    }

    /**
     * Get the value of finalizado
     */
    public function getFinalizado() {
        return $this->finalizado;
    }

    /**
     * Set the value of finalizado
     */
    public function setFinalizado($finalizado): self {
        $this->finalizado = $finalizado;
        return $this;
    }
}