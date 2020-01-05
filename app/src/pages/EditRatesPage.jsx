import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"
import Select from 'react-select'
import { endOfISOWeek } from 'date-fns'

export default function EditRatesPage() {
	const history = useHistory()

	const [errorContent, setErrorContent] = useState("")
	const [hourlyRates, setHourlyRates] = useState("")
	const [transportRates, setTransportRates] = useState("")
	const [hourlyRatesIsLoading, setHourlyRatesIsLoading] = useState(true)
	const [transportRatesIsLoading, setTransportRatesIsLoading] = useState(true)
	const [selectedHourlyRate, setSelectedHourlyRate] = useState(true)
	const [selectedTransportRate, setSelectedTransportRate] = useState(true)

	useEffect(() => {
		if(!localStorage.getItem('bearer') || !localStorage.getItem('user')) {
		history.push('/login')
	} else {
		const user = JSON.parse(localStorage.getItem('user'))
		getAllHourlyRates()
		getAllTransportRates()
		setSelectedHourlyRate({value: user.hourlyRate.id, label: user.hourlyRate.price + ' ' + user.hourlyRate.unit})
		setSelectedTransportRate({value: user.transportRate.id, label: user.transportRate.price + ' ' + user.transportRate.unit})
	}

	}, [])

	const handleData = (data, kind) => {
		let entities = []
		for(let entity of data) {
			entities.push({value: entity.id, label: entity.price + ' ' + entity.unit})
		}
		console.log(entities)

		switch(kind) {
			case "hourly":
				setHourlyRates(entities)
				setHourlyRatesIsLoading(false)
				break
			
			case "transport":
				setTransportRates(entities)
				setTransportRatesIsLoading(false)
				break

			default:
				break
		}
	}

	const getAllHourlyRates = () => {
		const token = localStorage.getItem('bearer')
		fetch('http://localhost:8000/api/hourlyRates/getAll', {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
		})
		.then(response => response.json())
		.then(data => {
			if(data.code) 
				handleBadJWT()
			else
				handleData(data, 'hourly')
		})
		.catch(error => console.error(error))
	}

	const getAllTransportRates = () => {
		const token = localStorage.getItem('bearer')
		fetch('http://localhost:8000/api/transportRates/getAll', {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
		})
		.then(response => response.json())
		.then(data => {
			if(data.code) 
				handleBadJWT()
			else
				handleData(data, 'transport')
		})
		.catch(error => console.error(error))
	}

	const updateRates = () => {
		const token = localStorage.getItem('bearer')
		const user = JSON.parse(localStorage.getItem('user'))

		const data = {
			'employee_id': user.id,
			'hourlyRate_id': selectedHourlyRate.value,
			'transportRate_id': selectedTransportRate.value,
		}

		fetch('http://localhost:8000/api/rates/update', {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify(data)
		})
		.then(response => response.json())
		.then(data => {
			if(data.code) 
				handleBadJWT()
			else {
				if(data.status == "400")
					setErrorContent(<p className="error">{data.message}</p>)
				else {
					setErrorContent("")
					fetchUserByEmail()
					history.push('/profile')
				}
				console.log(data)
			}
		})
		.catch(error => console.error(error))
	}

	const fetchUserByEmail = () => {
		const token = localStorage.getItem('bearer')
		const user = JSON.parse(localStorage.getItem('user'))
		fetch('http://localhost:8000/api/users/getByEmail', {
			method: 'POST',
			headers: {
				'Accept': 'application/json',
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify({
				email: user.email,
			})
		})
		.then(response => response.json())
		.then(data => {
			localStorage.setItem('user', JSON.stringify(data))
		})
		.catch(error => console.error(error)) // Prints result from `response.json()` in getRequest
	}

	const handleBadJWT = () => {
		alert("Session timed out. Please log in.")
		localStorage.removeItem('bearer')
		localStorage.removeItem('user')
		history.push('/login')
	}

	const handleSubmit = (e) => {
		e.preventDefault()
		updateRates()
	}

	return (
		<div className="container">
			<div className='card'>
				<h2>Tarieven aanpassen</h2>
				{errorContent}
				<form onSubmit={handleSubmit} method="post">
					<label>
						Uurloon
						<Select 
						onChange={(selectedRate) => setSelectedHourlyRate(selectedRate)}
						options={hourlyRates}
						isLoading={hourlyRatesIsLoading}
						isClearable={true}
						isSearchable={true}
						value={selectedHourlyRate}
						/>
					</label>
					<label>
						Transport Vergoeding
						<Select 
						onChange={(selectedRate) => setSelectedTransportRate(selectedRate)}
						options={transportRates}
						isLoading={transportRatesIsLoading}
						isClearable={true}
						isSearchable={true}
						value={selectedTransportRate}
						/>
					</label>
					<input className='button' type="submit" value="Opslaan" />
				</form>
			</div>
		</div>
	)
}
