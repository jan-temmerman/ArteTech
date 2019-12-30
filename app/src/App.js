import React from 'react'
import './App.sass'
import {
  BrowserRouter as Router,
  Switch,
  Route,
} from "react-router-dom"

import LoginPage from './pages/LoginPage'
import AddTaskPage from './pages/AddTaskPage'
import TasksPage from './pages/TasksPage'

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
          <Route exact path="/login">
            <LoginPage />
          </Route>
          <Route exact path="/">
            <AddTaskPage />
          </Route>
          <Route exact path="/tasks">
            <TasksPage />
          </Route>
        </Switch>
    </Router>
  )
}

export default App;
