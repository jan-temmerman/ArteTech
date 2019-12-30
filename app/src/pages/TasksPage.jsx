import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"

export default function TasksPage() {
	const history = useHistory()

	const [tasks, setTasks] = useState([])
	
	useEffect(() => {
		if(!localStorage.getItem('bearer') || !localStorage.getItem('user')) {
			history.push('/login')
		} else
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

		})
		.catch(error => console.error(error))
	}

	const handleBadJWT = () => {
		alert("Session timed out. Please log in.")
		localStorage.removeItem('bearer')
		localStorage.removeItem('user')
		history.push('/login')
	}

	const getTimeString = (datetimeString) => {
		let time = new Date(datetimeString)
		let timeString = time.getHours() + ':' + time.getMinutes()

		return timeString
	}

	const getDateString = (datetimeString) => {
		let date = new Date(datetimeString)
		let dateString = date.getDate() + '/' + (date.getMonth() + 1)+ '/' + date.getFullYear()

		return dateString
	}

	let content = []
	let last2dates = ["", ""]
	tasks.forEach((task, key) => {
		last2dates.shift()
		last2dates.push(getDateString(task.date))

		if(last2dates[1] != last2dates[0])
			content.push(
				<h3 style={{marginBottom: 0,}} key={key + "a"}>{last2dates[1]}</h3>
			)

		content.push(
			<div key={key} className="task_container">
				<p>{getTimeString(task.start_time)} - {getTimeString(task.end_time)}</p>
				<p>{task.activities_done}</p>
			</div>
		)
	});

	return (
		<div className="container">
			<div className='card'>
				<h2>Taken</h2>
				{content}
			</div>
		</div>
	)
}
