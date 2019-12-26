import React, { useState, useEffect } from 'react'
import { useHistory } from "react-router-dom"
import Select from 'react-select'

export default function AddTaskPage() {
	const history = useHistory()

	const [materials, steMaterials] = useState("")
	const [companies, setCompanies] = useState([])
	const [comapniesIsLoading, setCompaniesIsLoading] = useState(true)
	const [selectedCompany, setSelectedCompany] = useState("")

	useEffect(() => {
		console.log(selectedCompany)
		return
	}, [selectedCompany])
	
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

	const getActiveCompanies = (periods) => {
		let companies = []
			for(let period of periods) {
				if(new Date(period.startDate) <= new Date() && new Date() <= new Date(period.endDate))
					companies.push({value: period.company.name, label: period.company.name})
			}
		setCompanies(companies)
		setCompaniesIsLoading(false)
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
			getActiveCompanies(data)
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

	const getPeriodsByCompany = (companyName) => {
		
	}

	const handleSubmit = (e) => {
		e.preventDefault()
		postTask()
	}

	const companySelectHandler = (selectedComp) => {
		setSelectedCompany(selectedComp)
		getPeriodsByCompany(selectedComp)
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
							isSearchable={true}/>
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
