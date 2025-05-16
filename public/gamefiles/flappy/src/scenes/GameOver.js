export class GameOver extends Phaser.Scene {
    constructor() {
        super('GameOver');
    }

    init (data)
    {
        this.score = data.score;
        this.beforeUnloadHandler = data.beforeUnloadHandler;
    }

    create() {
        this.background1 = this.add.image(0, 0, 'background').setOrigin(0);

        this.add.text(this.scale.width * 0.5, this.scale.height * 0.5, 'Game Over', {
            fontFamily: 'Arial Black', fontSize: 64, color: '#ffffff',
            stroke: '#000000', strokeThickness: 8,
            align: 'center'
        }).setOrigin(0.5);

        const scoreText = this.add.text(this.scale.width * 0.5, this.scale.height * 0.5, 'Your Score: ' + this.score, {
            fontFamily: 'Arial Black', fontSize: 12, color: '#ffffff',
            stroke: '#000000', strokeThickness: 8,
            align: 'center'
        }).setOrigin(0.5,-3);

        const submitScoreText = this.add.text(this.scale.width * 0.5, this.scale.height * 0.5, 'Submit Score', {
            fontFamily: 'Arial Black', fontSize: 14, color: '#ffffff',
            stroke: '#000000', strokeThickness: 8,
            align: 'center'
        }).setOrigin(0.5,-5);

        //Fullscreen toggle
        const fullscreenText = this.add.text(this.scale.width - 30, 30, '⇱', {
            fontFamily: 'Arial Black', fontSize: 50, color: '#ffffff',
            stroke: '#000000', strokeThickness: 5,
            align: 'center'
        }).setOrigin(0.5).setDepth(100);
    
        fullscreenText.setInteractive().on('pointerup', function() {
            if (this.scene.scale.isFullscreen) {
                this.scene.scale.stopFullscreen();
                fullscreenText.setText('⇱');
            } else {
                this.scene.scale.startFullscreen();
                fullscreenText.setText('⇲');
            }
        });

        // the important part of the text
        submitScoreText.setInteractive();

        submitScoreText.once('pointerup', async function ()
        {
            submitScoreText.text = "Submitting score...";

            //using the submitScore embedded in the game page
            var result = await submitScore(this.score);

            window.removeEventListener("beforeunload", this.beforeUnloadHandler);
            submitScoreText.text = "Score submitted!";

        }, this);

    }
}
