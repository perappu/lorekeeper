export default {
    // 'audio': {
    //     score: {
    //         key: 'sound',
    //         args: ['assets/sound.mp3', 'assets/sound.m4a', 'assets/sound.ogg']
    //     },
    // },
    'image': {
        spikes: {
            key: 'spikes',
            args: ['/gamefiles/flappy/assets/spikes.png']
        }
    },
    'spritesheet': {
        player: {
            key: 'player',
            args: ['/gamefiles/flappy/assets/player.png', {
                frameWidth: player_x,
                frameHeight: player_y,
            }]
        },
        coin: {
            key: 'coin',
            args: ['/gamefiles/flappy/assets/coin.png', {
                frameWidth: coin_x,
                frameHeight: coin_y
            }]
        },
    }
};