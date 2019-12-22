import React, { useState } from 'react'

export default function LoginPage() {
    const [email, setEmail] = useState("")
	const [password, setPassword] = useState("")
	const [errorContent, setErrorContent] = useState("")

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
			else
				fetchUserByEmail(data.token)
		})
		.catch(error => console.error(error))
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
			console.log(data) // Prints result from `response.json()` in getRequest
		})
		.catch(error => console.error(error)) // Prints result from `response.json()` in getRequest
	}

  	const handleSubmit = (e) => {
		e.preventDefault()
		fetchJWT()
  	}

	return (
		<div className="container">
			<div className="login_header">
				<h1>ArteTech Login</h1>
			</div>
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
							autoComplete="current-password" />
					</label>
					<input className='button' type="submit" value="Sign in" />
				</form>
			</div>
		</div>
	)
}
