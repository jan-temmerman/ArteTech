import React, { useState, useEffect } from 'react'
import { useHistory, useParams } from "react-router-dom"
import Loader from 'react-loader-spinner'

export default function TasksPage() {
	const history = useHistory()
	const { id } = useParams()

	const [task, setTask] = useState("")
	const [isFetching, setIsFetching] = useState(true)
	const [errorContent, setErrorContent] = useState("")
	const [date, setDate] = useState("")
	const [startTime, setStartTime] = useState("")
	const [endTime, setEndTime] = useState("")
	const [pause, setPause] = useState("")
	const [companyName, setCompanyName] = useState("")
	const [activity, setActivity] = useState("")
	const [materialsUsed, setMaterialsUsed] = useState("")
	const [km, setKm] = useState(0)
	
	useEffect(() => {
		if(!localStorage.getItem('bearer') || !localStorage.getItem('user')) {
			history.push('/login')
		} else
		fetchTaskById(id)
		return
	}, [])

	useEffect(() => {
		console.log(task)
		if(task != "") {
			console.log("init")
			setDate(getDateString(task.date))
			setStartTime(getTimeString(task.start_time))
			setEndTime(getTimeString(task.end_time))
			setPause(getTimeString(task.pauseLength.time))
			setCompanyName(task.period.company.name)
			setActivity(task.activities_done)
			setMaterialsUsed(task.materialsUsed)
			setKm(task.km_traveled)
		}
	}, [task])
	
	const fetchTaskById = (taskId) => {
		const token = localStorage.getItem('bearer')

		fetch('http://localhost:8000/api/tasks/getById', {
			method: 'POST',
			headers: {
			'Accept': 'application/json',
			'Content-Type': 'application/json',
			'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify({
				id: taskId,
			})
		})
		.then(response => response.json())
		.then(data => {
			if(data) {
				if(data.code) 
					handleBadJWT()
				else {
					setTask(data)
				}
			} else
				setErrorContent("No Task Found...")
			
			setIsFetching(false)

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
		let timeString = time.getUTCHours() + ':' + time.getMinutes()

		return timeString
	}

	const getDateString = (datetimeString) => {
		let date = new Date(datetimeString)
		let dateString = date.getDate() + '/' + (date.getMonth() + 1)+ '/' + date.getFullYear()

		return dateString
	}

	return (
		<div className="container">
			<div className='card'>
				<h2>Taak</h2>
				{errorContent}
				<div style={{alignSelf: 'center'}}>
					<Loader
						type="Puff"
						color="#bbbbbb"
						height={50}
						width={50}
						visible={isFetching}
					/>
				</div>
				<div style={{width: '80%', alignSelf: 'center'}}>
					<h3>Datum: </h3>
					<p>{date}</p>

					<h3>Gewerkt Van Tot</h3>
					<p>{startTime} - {endTime}</p>

					<h3>Gewerkt Voor</h3>
					<p>{companyName}</p>

					<h3>Werk Verricht</h3>
					<p>{activity}</p>

					<h3>Materiaal Gebruikt</h3>
					<p>{materialsUsed}</p>

					<h3>Lengte Pauze</h3>
					<p>{pause}</p>

					<h3>Km's afgelegd</h3>
					<p>{km}</p>
				</div>
				
			</div>
		</div>
	)
}
