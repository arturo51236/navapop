<?php

class Usuario {
    private $id;
    private $nombre;
    private $email;
    private $passwd;
    private $telefono;
    private $poblacion;
    private $foto;
    private $sid;

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
     * Get the value of email
     */
    public function getEmail() {
        return $this->email;
    }

    /**
     * Set the value of email
     */
    public function setEmail($email): self {
        $this->email = $email;
        return $this;
    }

    /**
     * Get the value of passwd
     */
    public function getPasswd() {
        return $this->passwd;
    }

    /**
     * Set the value of passwd
     */
    public function setPasswd($passwd): self {
        $this->passwd = $passwd;
        return $this;
    }

    /**
     * Get the value of telefono
     */
    public function getTelefono() {
        return $this->telefono;
    }

    /**
     * Set the value of telefono
     */
    public function setTelefono($telefono): self {
        $this->telefono = $telefono;
        return $this;
    }

    /**
     * Get the value of poblacion
     */
    public function getPoblacion() {
        return $this->poblacion;
    }

    /**
     * Set the value of poblacion
     */
    public function setPoblacion($poblacion): self {
        $this->poblacion = $poblacion;
        return $this;
    }

    /**
     * Get the value of foto
     */
    public function getFoto() {
        return $this->foto;
    }

    /**
     * Set the value of foto
     */
    public function setFoto($foto): self {
        $this->foto = $foto;
        return $this;
    }

    /**
     * Get the value of sid
     */
    public function getSid() {
        return $this->sid;
    }

    /**
     * Set the value of sid
     */
    public function setSid($sid): self {
        $this->sid = $sid;
        return $this;
    }
}