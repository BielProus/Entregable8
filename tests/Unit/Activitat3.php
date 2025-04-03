<?php

class TascaNoFoundException extends Exception {}

class GestorDeTasques {

    private $tasques = [];


    public function __construct() {
        $this->tasques = [];
    }

    public function afegirTasca($titol, $descripcio, $dataLlimit) {

        if (empty($titol) || empty($descripcio)) {
            throw new InvalidArgumentException('El títol i la descripció no poden ser buits.');
        }

        $novaTasca = new Tasca($titol, $descripcio, $dataLlimit);

        $this->tasques[] = $novaTasca;
    }

    public function eliminarTasca($titol) {
        foreach ($this->tasques as $index => $tasca) {
            if ($tasca->titol === $titol) {
                unset($this->tasques[$index]);
                $this->tasques = array_values($this->tasques);
                return;
            }
        }

        throw new TascaNoFoundException("Tasca amb títol '$titol' no trobada.");
    }

    public function actualitzarEstatTasca($titol, $estat) {
        foreach ($this->tasques as $tasca) {
            if ($tasca->titol === $titol) {
                $tasca->actualitzarEstat($estat);
                return;
            }
        }

        throw new TascaNoFoundException("Tasca amb títol '$titol' no trobada.");
    }

    public function filtrarTasquesPerEstat($estat) {
        $resultat = [];
        
        // Filtrar les tasques segons l'estat
        foreach ($this->tasques as $tasca) {
            if ($tasca->estat === $estat) {
                $resultat[] = $tasca;
            }
        }

        return $resultat;
    }

    public function llistarTasques() {
        return $this->tasques;
    }
}
