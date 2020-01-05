import React, { useState, useEffect } from 'react'
import { useHistory, Link } from "react-router-dom"
import Loader from 'react-loader-spinner'

export default function ProfilePage() {
	const history = useHistory()

	const [user, setUser] = useState("")
	const [errorContent, setErrorContent] = useState("")
	const [hoursWorked, setHoursWorked] = useState("")
	
	useEffect(() => {
		if(!localStorage.getItem('bearer') || !localStorage.getItem('user')) {
			history.push('/login')
		} else
			setUser(JSON.parse(localStorage.getItem('user')))
			fetchWorkStats()
		return
	}, [])

	const handleBadJWT = () => {
		alert("Session timed out. Please log in.")
		localStorage.removeItem('bearer')
		localStorage.removeItem('user')
		history.push('/login')
	}


	const logoutHanldler = () => {
		localStorage.removeItem('user')
		localStorage.removeItem('bearer')
	}

	const fetchWorkStats = () => {
		const user = JSON.parse(localStorage.getItem('user'))
		const token = localStorage.getItem('bearer')
		fetch('http://localhost:8000/api/users/getWorkStats', {
			method: 'POST',
			headers: {
			'Accept': 'application/json',
			'Content-Type': 'application/json',
			'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify({
				employee_id: user.id,
			})
		})
		.then(response => response.json())
		.then(data => {
			if(data) {
				if(data.code) 
					handleBadJWT()
				else {
					setHoursWorked(data.hoursWorked)
				}
			} else
				setErrorContent("No stats found...")

		})
		.catch(error => console.error(error))
	}

	let freelancerContent = ""
	if(user != "") {
		if(user.status.status === 'freelancer')
			freelancerContent = 
				<div>
					<h3>Uurloon</h3>
					<p>{user.hourlyRate.price} {user.hourlyRate.unit}</p>

					<h3>Transport Vergoeding</h3>
					<p>{user.transportRate.price} {user.transportRate.unit}</p>
				</div>
	}	

	let freelancerActions = ""
	if(user != "") {
		if(user.status.status === 'freelancer')
			freelancerActions = <Link to="/editRates" className='button'> Tarieven Aanpassen </Link>
	}

	if(user != "")
		return (
			<div className="container">
				<div className='card'>
					<h2>Profiel</h2>

					<h3>Naam</h3>
					<p>{user.firstName} {user.lastName}</p>

					<h3>Email</h3>
					<p>{user.email}</p>

					<h3>Status</h3>
					<p>{user.status.status}</p>

					<h3>Totaal aantal uren gewerkt</h3>
					<p>{hoursWorked}</p>

					{freelancerContent}
					<div className="actions_container">
						<Link to="/login" onClick={() => logoutHanldler()} className='button'>
							Logout
						</Link>
						{freelancerActions}
					</div>
				</div>
			</div>
		)
	else
		return (
			""
		)
}
