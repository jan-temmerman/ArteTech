import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"

export default function LoginPage() {
	const history = useHistory()

    const [email, setEmail] = useState("")
	const [password, setPassword] = useState("")
	const [errorContent, setErrorContent] = useState("")

	useEffect(() => {
		if(localStorage.getItem('bearer') && localStorage.getItem('user'))
			history.push('/')
		return
	}, [])

	const handleInputChange = (event) => {
		const target = event.target
		const value = target.value
		const name = target.name

		switch(name) {
			case "email":
				setEmail(value)
				break
			case "password":
				setPassword(value)
				break
			default:
				break
		}
	}
	
	const fetchJWT = () => {
		fetch('http://localhost:8000/api/login_check', {
			method: 'POST',
			headers: {
			'Accept': 'application/json',
			'Content-Type': 'application/json'
			},
			body: JSON.stringify({
				username: email,
				password: password
			})
		})
		.then(response => response.json())
		.then(data => {
			console.log(data)
			if(data.code)
				setErrorContent(<p className="error">Invalid credentials.</p>)
			else {
				fetchUserByEmail(data.token)
			}
		})
		.catch(error => console.error(error))
	}

	const loginHandler = (token, user) => {
		setErrorContent("")
		localStorage.setItem('user', JSON.stringify(user))
		localStorage.setItem('bearer', token)
		history.push('/')
	}

	const fetchUserByEmail = (token) => {
		fetch('http://localhost:8000/api/users/getByEmail', {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify({
				email: email,
			})
		})
		.then(response => response.json())
		.then(data => {
			if(data.status.status === 'employee' || data.status.status === 'freelancer')
				loginHandler(token, data)
			else
				setErrorContent(<p className="error">This portal is for employees and freelancers only.</p>)
		})
		.catch(error => console.error(error)) // Prints result from `response.json()` in getRequest
	}

  	const handleSubmit = (e) => {
		e.preventDefault()
		fetchJWT()
  	}

	return (
		<div className="container">
			<div className='card'>
				<h2>Please sign in</h2>
				{errorContent}
				<form onSubmit={handleSubmit} method="post">
					<label>
						Email
						<input
							name="email"
							type="email"
							value={email}
							onChange={handleInputChange}
							autoComplete="email" />
					</label>
					<label>
						Password
						<input
							name="password"
							type="password"
							value={password}
							onChange={handleInputChange}
							autoComplete="password" />
					</label>
					<input className='button' type="submit" value="Sign in" />
				</form>
			</div>
		</div>
	)
}
