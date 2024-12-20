// Simuler URL.createObjectURL et URL.revokeObjectURL
global.URL.createObjectURL = jest.fn();
global.URL.revokeObjectURL = jest.fn();

// Simuler window.alert
global.alert = jest.fn();

const fs = require('fs');
const path = require('path');
const { 
    createPlayerFields, 
    startGame, 
    displayFeature, 
    submitVotes, 
    validateVotes, 
    calculateAverage,
    getPlayerNames 
} = require('./app');

// Charger le contenu HTML pour chaque test
const html = fs.readFileSync(path.resolve(__dirname, './index.html'), 'utf8');

describe('App Functions', () => {
  beforeEach(() => {
    document.documentElement.innerHTML = html.toString();
    // Ajouter les éléments nécessaires au DOM
    document.body.innerHTML += `
      <div id="config"></div>
      <div id="backlog"></div>
      <div id="game" style="display: none;"></div>
    `;
  });

  test('should create player fields', () => {
    document.getElementById('numPlayers').value = 3;
    createPlayerFields();
    expect(document.getElementById('player-fields').children.length).toBe(3);
  });

  test('should start the game and display feature', () => {
    document.getElementById('numPlayers').value = 2;
    createPlayerFields();
    document.getElementById('player0-name').value = 'Alice';
    document.getElementById('player1-name').value = 'Bob';
    startGame();
    expect(document.getElementById('game').style.display).toBe('block');
    expect(getPlayerNames()).toEqual(['Alice', 'Bob']);
  });

  test('should calculate average votes correctly', () => {
    const votes = [1, 2, 3, 4];
    const avg = calculateAverage(votes);
    expect(avg).toBe(2.5);
  });

  // Test basique
  test('basic test', () => {
    expect(1 + 1).toBe(2);
  });

  // Ajoutez plus de tests selon vos besoins
});
