import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"

export default function AddTaskPage() {
	const history = useHistory()

	const [email, setEmail] = useState("")
	
	useEffect(() => {
		console.log(localStorage.getItem('bearer'))
		if(localStorage.getItem('bearer') == null) {
			history.push('/login')
		}
		return
	}, [])

	return (
		<div className="container">
			<div className="login_header">
				<h1>ArteTech Login</h1>
			</div>
			<div className='card'>
				<h2>Add a Task</h2>

			</div>
		</div>
	)
}
