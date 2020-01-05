import React, { useState, useEffect } from 'react'
import { useHistory, Link } from "react-router-dom"
import Loader from 'react-loader-spinner'

export default function ProfilePage() {
	const history = useHistory()

	const [user, setUser] = useState("")
	
	useEffect(() => {
		if(!localStorage.getItem('bearer') || !localStorage.getItem('user')) {
			history.push('/login')
		} else
			setUser(JSON.parse(localStorage.getItem('user')))
		return
	}, [])

	const logoutHanldler = () => {
		localStorage.removeItem('user')
		localStorage.removeItem('bearer')
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

					<h3>Uurloon</h3>
					<p>{user.hourlyRate.price} {user.hourlyRate.unit}</p>

					<h3>Transport Vergoeding</h3>
					<p>{user.transportRate.price} {user.transportRate.unit}</p>

					<Link to="/login" onClick={() => logoutHanldler()} className='button'>
						Logout
					</Link>
				</div>
			</div>
		)
	else
		return (
			""
		)
}
