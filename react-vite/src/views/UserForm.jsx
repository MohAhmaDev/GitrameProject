import React, { useEffect, useState } from 'react'
import { useNavigate, useParams } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'

export default function UserForm() {

  const {id} = useParams()
  const [loading, setLoading] = useState(false)
  const [errors, setErrors] = useState(null)
  const {setNotification} = useStateContext()
  const navigate = useNavigate();

  const [user, setUser] = useState({
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  })

  if (id) {
    useEffect(() => {
      setLoading(null)
      axiosClient.get(`/users/${id}`)
      .then(({data}) => {
        setLoading(false)
        setUser(data.data)
      })
      .catch(() => {
        setLoading(false)
      })
    }, [])
  }

  const onSubmit = (ev) => {
    ev.preventDefault();
    if (user.id) {
      console.log("put")
      axiosClient.put(`/users/${user.id}`, user)
      .then(() => {
        //TODO show notification
        setNotification("User was successfuly create")
        navigate('/users')
      })
      .catch(err => {
        const response = err.response;
        console.log("put failed")

        if (response && response.status === 422) {
          setErrors(response.data.errors)
        }
      })
    } else {
      axiosClient.post('/users', user)
        .then(() => {
          navigate('/users')
        })
        .catch(err => {
          const response = err.response;
          if (response && response.status === 422) {
            setErrors(response.data.errors)
          }
        })
    }
  }

  return (
    <>
      {user.id && <h1> Update user: {user.name} </h1>}
      {!user.id && <h1> New user </h1>}
      <div className="card animated fadeInDown">
      {loading && (
          <div className="text-center">
            Loading...
          </div>
        )}
        {errors &&
          <div className="alert">
            {Object.keys(errors).map(key => (
              <p key={key}>{errors[key][0]}</p>
            ))}
          </div>
        }
        {!loading &&
          <form onSubmit={onSubmit}>
            <input onChange={ev => setUser({...user, name: ev.target.value})}  value={user.name} type="text" placeholder='Name'/>
            <input onChange={ev => setUser({...user, email: ev.target.value})}  value={user.email} type="email" placeholder='Email' />
            <input onChange={ev => setUser({...user, password: ev.target.value})}  type="password" placeholder='Password' />
            <input onChange={ev => setUser({...user, password_confirmation: ev.target.value})}  type="password" placeholder='Password Confirmation'/>
            <button className='btn'> Save </button>
          </form>
        }
      </div>
    </>
  )
}
