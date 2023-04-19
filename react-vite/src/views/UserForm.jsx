import React, { useEffect, useState } from 'react'
import { useLocation, useNavigate, useParams, useSearchParams } from 'react-router-dom'
import axiosClient from '../axios-client'
import { useStateContext } from '../contexts/ContextProvider'
import { 
  Box,
  Button,
  Select,
  MenuItem,
  FormControl,
  InputLabel,
} from '@mui/material'

import { useForm, Controller } from 'react-hook-form'


export default function UserForm() {

                 
  const [filiale, setFiliale] = useState(null)
  const [filiales, setFiliales] = useState()
  const [role, setRole] = useState("")
  const [roles, setRoles] = useState()
  const [userID, setUserID] = useState(null);

  const {id} = useParams()
  const [loading, setLoading] = useState(false)
  const [additing, setAdditing] = useState(false)
  const [addFiliale, setAddFiliale] = useState(false)
  const [errorsServer, setErrorsServer] = useState(null)
  const {setNotification} = useStateContext()
  const navigate = useNavigate();
  const location = useLocation();



  const { handleSubmit, control, setError, register, formState, formState: { errors } } = useForm({
    mode: "onChange"
  });

  const [user, setUser] = useState({
    id: null,
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
  })

  useEffect(() => {
    location.pathname.startsWith('/users/filiale/') && setAddFiliale(true)
  }, [])

  const getRoles = () => {
    axiosClient.get('/roles').then(({data}) => {
      setRoles(data.role)
    }).catch((error) => {
      console.log(error)
    })
  }

  const getFiliales = () => {
    axiosClient.get('/filiale').then(({data}) => {
      setFiliales(data.filiale)
    }).catch((error) => {
      console.log(error)
    })
  }

  if (id) {
    useEffect(() => {
      setLoading(null)
      axiosClient.get(`/users/${id}`)
      .then(({data}) => {
        setLoading(false);
        setUser(data.data);
        setRole(data.data.role); 
        setFiliale(data.data.filiale.id);   
        getRoles();
        getFiliales();
    
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
        setNotification("User was successfuly Update")
        navigate('/users')
      })
      .catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setErrorsServer(response.data.errors)
        }
      })
    } else {
    axiosClient.post('/users', user)
      .then(({data}) => {
        // navigate('/users')
        setAdditing(true);
        setRole("basic")
        getRoles();
        setUserID(data.id);
        getFiliales()

      })
      .catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setErrorsServer(response.data.errors)
        }
      })
    }
  }


  useEffect(() => {
    getFiliales()
  }, [])

  const onValid = (data) => {
    axiosClient.put(`/roles/${id ? id : userID}`, {
      role_name: data.role
    }).then(response => {
      console.log(response.data)
      setNotification("Role User was successfuly Update")
      id ? navigate('/users') : setAddFiliale(true)
    }).catch(error => {
      console.log(error);
    });
  }
  const onFocus = (data) => {
    // console.log("Filiale", data.filiale)
    if (!addFiliale) 
    {
      axiosClient.put(`/filiale/${id}`, {
        filiale_id: data.filiale
      }).then(response => {
        console.log(response.data);
        setNotification("Filiale User was successfuly Update")
        navigate('/users')
      }).catch(error => {
        const response = err.response;
        if (response && response.status === 422) {
          setErrorsServer(response.data.errors)
        }
      });    
      console.log("01", data.filiale)
    } else {
      axiosClient.post(`/filiale/${id ? id : userID}`, {
        filiale_id: data.filiale
      }).then(response => {
        console.log(response.data);
        setNotification("Filiale User was successfuly Update")
        navigate('/users')
      }).catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setErrorsServer(response.data.errors)
        }
      })
      console.log("02",  data.filiale)
    }
  }


  return (
    <>
      {user.id && <h1> Update user: {user.name} </h1>}
      {!user.id && <h1> New user </h1>}
      {(!additing && !addFiliale) && <div className="card animated fadeInDown">
      {loading && (
          <div className="text-center">
            Loading...
          </div>
      )}
        {errorsServer &&
          <div className="alert">
            {Object.keys(errorsServer).map(key => (
              <p key={key}>{errorsServer[key][0]}</p>
            ))}
          </div>
        }
        {(!loading && !additing && !addFiliale) &&
          <form onSubmit={onSubmit}>
            <input onChange={ev => setUser({...user, name: ev.target.value})}  value={user.name} type="text" placeholder='Name'/>
            <input onChange={ev => setUser({...user, email: ev.target.value})}  value={user.email} type="email" placeholder='Email' />
            <input onChange={ev => setUser({...user, password: ev.target.value})}  type="password" placeholder='Password' />
            <input onChange={ev => setUser({...user, password_confirmation: ev.target.value})}  type="password" placeholder='Password Confirmation'/>
            <button className='btn'> Save </button>
          </form>}
      </div>}

      {(role && !addFiliale) &&
        <div className="card animated fadeInDown">
          <form onSubmit={handleSubmit(onValid)}>
            <Box m="20px">
              <Controller
                control={control}
                name="role"
                render={({ field: { onChange } }) => (
                  <FormControl variant="outlined" sx={{ width: "300px" }}>
                    <InputLabel id="demo-simple-select-label"> access role  </InputLabel>
                    <Select
                      label="role access"
                      value={role}
                      {...register("role")}
                      onChange={ (e) => { onChange(e); setRole(e.target?.value)} }
                    >
                      {roles?.map(role => (
                        <MenuItem value={role.name} key={role.id}> {role.name} </MenuItem>
                      ))}
                    </Select>
                  </FormControl>
                )}
              />  
              <Box display="flex" justifyContent="end" mt="20px">
                  <Button  type="submit" color="success" variant="contained">
                      {user.id ? "Update" : "Add"} Role user
                  </Button>
              </Box>
            </Box>
          </form>
        </div>
      }
      {(filiale || addFiliale) && <div className="card animated fadeInDown">
        <form onSubmit={handleSubmit(onFocus)} >
          <Box m="20px">
            <Controller
              control={control}
              name="filiale"
              render={({ field: { onChange } }) => (
                <FormControl variant="outlined" sx={{ width: "300px" }}>
                  <InputLabel id="demo-simple-select-label"> access filiale  </InputLabel>
                  <Select
                    label="filiale access"
                    value={filiale} 
                   {...register("filiale")}
                    onChange={e => {onChange(e); setFiliale(e.target.value);}} 
                  >
                    {filiales?.map(filiale => (
                      <MenuItem value={filiale.id} key={filiale.id}> {filiale.nom_filiale} </MenuItem>
                    ))}
                  </Select>
                </FormControl>
              )}
            />  
            <Box display="flex" justifyContent="end" mt="20px">
                <Button  type="submit" color="success" variant="contained">
                    {!addFiliale ? "Update" : "Add"} Filiale user
                </Button>
            </Box>
          </Box>
        </form>
      </div>}
    </>
  )
}
