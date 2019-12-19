import React from 'react';
import logo from './logo.svg';
import './App.css';

function App() {

  fetch('http://localhost:8000/api/login_check', {
    method: 'POST',
    headers: {
      'Accept': 'application/json',
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      username: "anna@gmail.com",
      password: "88FG4HK3"
    })
  })
  .then(response => response.json())
  .then(data => {
      console.log(data.token)
      fetch('http://localhost:8000/api/users', {
      method: 'GET',
      headers: {
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + data.token,
      },
    })
    .then(response => response.json())
    .then(data => {
      console.log(data) // Prints result from `response.json()` in getRequest
    })
    .catch(error => console.error(error)) // Prints result from `response.json()` in getRequest
  })
  .catch(error => console.error(error))

  return (
    <div className="App">
      <header className="App-header">
        <img src={logo} className="App-logo" alt="logo" />
        <p>
          Edit <code>src/App.js</code> and save to reload.
        </p>
        <a
          className="App-link"
          href="https://reactjs.org"
          target="_blank"
          rel="noopener noreferrer"
        >
          Learn React
        </a>
      </header>
    </div>
  );
}

export default App;
