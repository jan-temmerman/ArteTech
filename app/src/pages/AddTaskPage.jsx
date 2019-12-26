import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"
import Select from 'react-select'
import DatePicker from 'react-datepicker'

import "react-datepicker/dist/react-datepicker.css";

export default function AddTaskPage() {
	const history = useHistory()

	const [materials, steMaterials] = useState("")
	const [companies, setCompanies] = useState([])
	const [comapniesIsLoading, setCompaniesIsLoading] = useState(true)
	const [selectedCompany, setSelectedCompany] = useState("")
	const [periods, setPeriods] = useState([])
	const [periodsIsLoading, setPeriodsIsLoading] = useState(true)
	const [selectedPeriod, setSelectedPeriod] = useState("")
	const [selectIsDisabled, setSelectIsDisabled] = useState(true)
	const [date, setDate] = useState(new Date());
	const [startTime, setStartTime] = useState(new Date());
	const [endTime, setEndTime] = useState(new Date());	

	useEffect(() => {
		if(selectedCompany === null) {
			setSelectIsDisabled(true)
			setSelectedPeriod("")
		}

		return
	}, [selectedCompany])

	useEffect(() => {
		//getPauseLengths()

		if(periods.length > 0) {
	
			for(let period of periods) {
				console.log(period.value)
				console.log(selectedPeriod.value)
				if(period.value !== selectedPeriod.value)
					setSelectedPeriod("")
			}
		}

		return
	}, [periods])
	
	useEffect(() => {
		if(localStorage.getItem('bearer') == null) {
			history.push('/login')
		}

		getAllPeriods()

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

	const getActiveEntities = (entities, kind) => {
		let activeEntities = []
		for(let entity of entities) {
			if(new Date(entity.startDate) <= new Date() && new Date() <= new Date(entity.endDate)) {
				switch(kind) {
					case 'company':
						activeEntities.push({value: entity.company.name, label: entity.company.name})
						break

					case 'period':
						activeEntities.push({value: entity.name, label: entity.name})
						break
				}
			}
		}

		switch(kind) {
			case "company":
				console.log(entities)
				setCompanies(activeEntities)
				setCompaniesIsLoading(false)
				break
			
			case "period":
				setPeriods(activeEntities)
				setPeriodsIsLoading(false)
				break
		}
	}

	const getAllPeriods = () => {
		setCompaniesIsLoading(true)
		const token = localStorage.getItem('bearer')
		fetch('http://localhost:8000/api/periods/getAll', {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
		})
		.then(response => response.json())
		.then(data => {
			getActiveEntities(data, "company")
		})
		.catch(error => console.error(error))
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
				'materials_used': materials
			})
		})
		.then(response => response.json())
		.then(data => {
			console.log(data) // Prints result from `response.json()` in getRequest
		})
		.catch(error => console.error(error)) // Prints result from `response.json()` in getRequest
	}

	/*const getPauseLengths = () => {
		const token = localStorage.getItem('bearer')
		fetch('http://localhost:8000/api/pause_lengths', {
			method: 'GET',
			headers: {
				'Accept': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
		})
		.then(response => response.json())
		.then(data => {
			console.log(data) // Prints result from `response.json()` in getRequest
			for(let pause of data) {
				let given_seconds = new Date(pause.time).getTime()
				let hours = Math.floor(given_seconds / 3600); 
				let minutes = Math.floor((given_seconds - (hours * 3600)) / 60); 
				let seconds = given_seconds - (hours * 3600) - (minutes * 60); 
	
				let timeString = hours.toString().padStart(2, '0') + ':' + 
                minutes.toString().padStart(2, '0') + ':' + 
                seconds.toString().padStart(2, '0');
				console.log(timeString)
			}
		})
		.catch(error => console.error(error)) // Prints result from `response.json()` in getRequest
	}*/

	const getPeriodsByCompany = (companyName) => {
		const token = localStorage.getItem('bearer')
		fetch('http://localhost:8000/api/periods/getByCompany', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/json',
				'Authorization': 'Bearer ' + token,
			},
			body: JSON.stringify({
				'name': companyName
			})
		})
		.then(response => response.json())
		.then(data => {
			getActiveEntities(data, "period")
		})
		.catch(error => console.error(error)) // Prints result from `response.json()` in getRequest
	}

	const handleSubmit = (e) => {
		e.preventDefault()
		postTask()
	}

	const companySelectHandler = (selectedComp) => {
		setSelectedCompany(selectedComp)
		setSelectIsDisabled(false)
		getPeriodsByCompany(selectedComp)
	}

	const periodSelectHandler = (selectedPer) => {
		setSelectedPeriod(selectedPer)
	}

	return (
		<div className="container">
			<div className="login_header">
				<h1>ArteTech Login</h1>
			</div>
			<div className='card'>
				<h2>Taak toevoegen</h2>
				<form onSubmit={handleSubmit} method="post">
					<label>
						Klant
						<Select 
						onChange={(selectedCompany) => companySelectHandler(selectedCompany)}
						options={companies}
						isLoading={comapniesIsLoading}
						isClearable={true}
						isSearchable={true}
						/>
					</label>
					<label>
						Opdracht
						<Select 
						onChange={(selectedPeriod) => periodSelectHandler(selectedPeriod)}
						options={periods}
						isLoading={periodsIsLoading}
						isClearable={true}
						isSearchable={true}
						isDisabled={selectIsDisabled}
						value={selectedPeriod}
						/>
					</label>
					<label>
						Datum
						<DatePicker
						showPopperArrow={false}
						selected={date}
						onChange={date => setDate(date)}
						todayButton="Vandaag"
						dateFormat="dd/MM/yyyy"
						/>
					</label>
					<label>
						Start tijd
						<DatePicker
						selected={startTime}
						onChange={time => setStartTime(time)}
						showTimeSelect
						showTimeSelectOnly
						timeIntervals={15}
						timeCaption="Time"
						dateFormat="HH:mm:ss"
						/>
					</label>
					<label>
						Eind tijd
						<DatePicker
						selected={endTime}
						onChange={time => setEndTime(time)}
						showTimeSelect
						showTimeSelectOnly
						timeIntervals={15}
						timeCaption="Time"
						dateFormat="HH:mm:ss"
						/>
					</label>
					<label>
						Materialen
						<input
							name="materials"
							type="text"
							value={materials}
							onChange={handleInputChange}/>
					</label>
					<input className='button' type="submit" value="Voeg Toe" />
				</form>
			</div>
		</div>
	)
}
