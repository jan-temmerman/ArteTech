import React, { useState } from 'react'

export default function LoginPage() {
    const [email, setEmail] = useState("")
    const [password, setPassword] = useState("")

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
			fetchUserByEmail(data.token)
		})
		.catch(error => console.error(error))
	}

	const fetchUserByEmail = (token) => {
		fetch('http://localhost:8000/api/users', {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
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
		<form onSubmit={handleSubmit} method="post">
			<label>
				Email:
				<input
					name="email"
					type="email"
					value={email}
					onChange={handleInputChange} />
			</label>
			<br />
			<label>
				Password:
				<input
					name="password"
					type="password"
					value={password}
					onChange={handleInputChange} />
			</label>
			<input type="submit" value="Submit" />
		</form>
	)
}
