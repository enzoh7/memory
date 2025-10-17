<?php
class Card {
    private $id;
    private $imageUrl;
    private $isFlipped = false;
    private $isMatched = false;
    
    public static function createFromState($state) {
        $card = new self($state['id'], $state['imageUrl']);
        $card->isFlipped = $state['isFlipped'];
        $card->isMatched = $state['isMatched'];
        return $card;
    }
    
    public function getState() {
        return [
            'id' => $this->id,
            'imageUrl' => $this->imageUrl,
            'isFlipped' => $this->isFlipped,
            'isMatched' => $this->isMatched
        ];
    }

    public function toArray(): array {
        return [
            'id' => $this->id,
            'imageUrl' => $this->imageUrl,
            'isFlipped' => $this->isFlipped,
            'isMatched' => $this->isMatched
        ];
    }

    // Méthode statique pour créer une carte à partir d'un tableau
    public static function fromArray(array $data): Card {
        $card = new Card($data['id'], $data['imageUrl']);
        $card->isFlipped = $data['isFlipped'];
        $card->isMatched = $data['isMatched'];
        return $card;
    }

    public function __construct($id, $imageUrl) {
        $this->id = $id;
        $this->imageUrl = $imageUrl;
    }

    public function getId() {
        return $this->id;
    }

    public function getImageUrl() {
        return $this->imageUrl;
    }

    public function isFlipped() {
        return $this->isFlipped;
    }

    public function isMatched() {
        return $this->isMatched;
    }

    public function flip() {
        $this->isFlipped = true;
    }

    public function hide() {
        $this->isFlipped = false;
    }

    public function match() {
        $this->isMatched = true;
        $this->isFlipped = true;
    }

    public function render($index) {
        $cardState = $this->isFlipped || $this->isMatched ? 'flipped' : '';
        $imageUrl = $this->isFlipped || $this->isMatched ? $this->imageUrl : 'images/freeze.png';
        
        echo '<div class="card-wrapper">';
        echo '<a href="game.php?flip=' . $index . '" class="card ' . $cardState . '">';
        echo '<img src="' . htmlspecialchars($imageUrl) . '" alt="Carte" />';
        echo '</a>';
        echo '</div>';
    }
}
?>
