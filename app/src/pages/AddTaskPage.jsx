import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"

export default function AddTaskPage() {
	const history = useHistory()

	const [materials, steMaterials] = useState("")
	
	useEffect(() => {
		console.log(localStorage.getItem('bearer'))
		if(localStorage.getItem('bearer') == null) {
			history.push('/login')
		}
		return
	}, [])

	const handleInputChange = (event) => {
		const target = event.target
		const value = target.value
		const name = target.name

		switch(name) {
			case "materials":
				steMaterials(value)
				break
			default:
				break
		}
	}

	const postTask = () => {
		const token = localStorage.getItem('bearer')
		fetch('http://localhost:8000/api/tasks/setTask', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify({
				'employee_id': 9,
				'period_id': 1,
				'pause_id': 1,
				'date': "2019-12-22",
				'time': {
					'start': "08:00:00",
					'end': "17:12:00"
				},
				'materials_used': "jaa jan"
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
		postTask()
  	}

	return (
		<div className="container">
			<div className="login_header">
				<h1>ArteTech Login</h1>
			</div>
			<div className='card'>
				<h2>Add a Task</h2>
				<form onSubmit={handleSubmit} method="post">
					<label>
						Materials
						<input
							name="materials"
							type="text"
							value={materials}
							onChange={handleInputChange}/>
					</label>
					<input className='button' type="submit" value="Add Task" />
				</form>
			</div>
		</div>
	)
}
