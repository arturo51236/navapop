<?php

class Foto {
    private $id;
    private $foto;

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
}