function maximumWealth(lockers) {
  let maxWealth = -1;

  for (let i = 0; i < lockers.length; i++) {
    let ownerWealth = 0;
    for (let j = 0; j < lockers[i].length; j++) {
      ownerWealth += lockers[i][j];
    }
    maxWealth = Math.max(maxWealth, ownerWealth);
  }

  return maxWealth;
}

// Example 1
const lockers1 = [
  [5, 6, 7],
  [3, 4, 3],
];
console.log(maximumWealth(lockers1)); // Outputs: 18

// Example 2
const lockers2 = [
  [1, 5],
  [7, 3],
  [3, 5],
];
console.log(maximumWealth(lockers2)); // Outputs: 10
