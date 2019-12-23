import React from 'react'
import './App.sass'
import {
  BrowserRouter as Router,
  Switch,
  Route,
} from "react-router-dom"

import LoginPage from './pages/LoginPage'
import AddTaskPage from './pages/AddTaskPage'

function App() {

  return (
    <Router>
        {/*<nav>
          <ul>
            <li>
              <Link to="/">Home</Link>
            </li>
            <li>
              <Link to="/about">About</Link>
            </li>
            <li>
              <Link to="/users">Users</Link>
            </li>
          </ul>
        </nav>*/}

        <Switch>
          <Route path="/login">
            <LoginPage />
          </Route>
          <Route path="/">
            <AddTaskPage />
          </Route>
        </Switch>
    </Router>
  )
}

export default App;
