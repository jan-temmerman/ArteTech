import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"

export default function TasksPage() {
	const history = useHistory()

	const [tasks, setTasks] = useState([])
	
	useEffect(() => {
		fetchTasks()
		return
	}, [])
	
	const fetchTasks = () => {
		const token = localStorage.getItem('bearer')
		const user = JSON.parse(localStorage.getItem('user'))

		fetch('http://localhost:8000/api/tasks/getFromUser', {
			method: 'POST',
			headers: {
			'Accept': 'application/json',
			'Content-Type': 'application/json',
			'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify({
				id: user.id,
			})
		})
		.then(response => response.json())
		.then(data => {

			if(data.code) 
				handleBadJWT()
			else
				setTasks(data)


			console.log(data)
		})
		.catch(error => console.error(error))
	}

	const handleBadJWT = () => {
		alert("Session timed out. Please log in.")
		history.push('/login')
	}

	let content = []
	tasks.forEach((task, key) => {
	content.push(<p key={key}>{task.period.name}</p>)
	});

	return (
		<div className="container">
			<div className="login_header">
				<h1>ArteTech</h1>
			</div>
			<div className='card'>
				<h2>Taken</h2>
				{content}
			</div>
		</div>
	)
}
