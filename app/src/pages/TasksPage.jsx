import React, { useState, useEffect } from 'react'
import { useHistory, Link } from "react-router-dom"
import Loader from 'react-loader-spinner'

export default function TasksPage() {
	const history = useHistory()

	const [tasks, setTasks] = useState([])
	const [isFetching, setIsFetching] = useState(true)
	
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
			else {
				setTasks(data.reverse())
				setIsFetching(false)
			}

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
		let timeString = time.getUTCHours().toString().padStart(2, '0') + ':' + time.getMinutes().toString().padStart(2, '0')

		return timeString
	}

	const getDateString = (datetimeString) => {
		let date = new Date(datetimeString)
		let dateString = date.getDate().toString().padStart(2, '0') + '/' + (date.getMonth() + 1).toString().padStart(2, '0') + '/' + date.getFullYear().toString().padStart(2, '0')

		return dateString
	}

	let content = []
	let last2dates = ["", ""]
	tasks.forEach((task, key) => {
		last2dates.shift()
		last2dates.push(getDateString(task.date))

		if(last2dates[1] != last2dates[0])
			content.push(
				<h3 key={key + "a"}>{last2dates[1]}</h3>
			)

		content.push(
			<Link to={"/tasks/" + task.id} key={key} className="task_container">
				<div>
					<p>{getTimeString(task.start_time)} - {getTimeString(task.end_time)}</p>
					<p>{task.period.company.name}</p>
					<p>{task.activities_done}</p>
				</div>
			</Link>
		)
	});

	return (
		<div className="container">
			<div className='card'>
				<h2>Voltooide Taken</h2>
				<div style={{alignSelf: 'center'}}>
					<Loader
						type="Puff"
						color="#bbbbbb"
						height={50}
						width={50}
						visible={isFetching}
					/>
				</div>
				{content}
			</div>
		</div>
	)
}
