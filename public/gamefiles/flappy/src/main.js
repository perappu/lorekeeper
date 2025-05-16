import { Boot } from '/gamefiles/flappy/src/scenes/Boot.js';
import { Game } from '/gamefiles/flappy/src/scenes/Game.js';
import { GameOver } from '/gamefiles/flappy/src/scenes/GameOver.js';
import { Preloader } from '/gamefiles/flappy/src/scenes/Preloader.js';

var height = Math.max(document.documentElement.clientHeight || 0, window.innerHeight || 0);

const config = {
    type: Phaser.AUTO,
    width: 800,
    height: 600,
    parent: 'game-container',
    fullscreenTarget: 'game-container',
    backgroundColor: '#028af8',
    physics: {
        default: 'arcade',
        arcade: {
            debug: false,
            gravity: { y: 400 }
        }
    },
    scale: {
        mode: Phaser.Scale.FIT,
        autoCenter: Phaser.Scale.CENTER_HORIZONTALLY
    },
    scene: [
        Boot,
        Preloader,
        Game,
        GameOver
    ]
};

const game = new Phaser.Game(config);
  
game.scale.on(Phaser.Scale.Events.ENTER_FULLSCREEN, () => {
    game.scale.autoCenter = Phaser.Scale.CENTER_BOTH;
    game.scale.refresh();
});

game.scale.on(Phaser.Scale.Events.LEAVE_FULLSCREEN, () => {
    game.scale.autoCenter = Phaser.Scale.CENTER_HORIZONTALLY;
    game.scale.refresh();
  });

