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
  CircularProgress
} from '@mui/material'

import { useForm, Controller } from 'react-hook-form'
import { useDisplayContext } from '../contexts/DisplayContext'


export default function UserForm() {

                 
  const [filiale, setFiliale] = useState(null)
  const [role, setRole] = useState("")
  const [userID, setUserID] = useState(null);

  const {id} = useParams()
  const [additing, setAdditing] = useState(false)
  const [addFiliale, setAddFiliale] = useState(false)

  const { setNotification} = useStateContext()
  const {filiales, getFiliales, roles, getRoles, loading, setLoading} = useDisplayContext();
  const navigate = useNavigate();
  const location = useLocation();
  // const [loading, setLoading] = useState(true)



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
    if (location.pathname.startsWith('/users/filiale/')) {
        setAddFiliale(true);
        getFiliales();
    } 
    if (location.pathname.startsWith('/users/new')) {
      console.log("loading")
      setLoading(true);
    }    
  }, [])


  const getUser = () => {
    axiosClient.get(`/users/${id}`)
    .then(({data}) => {
      setUser(data.data);
      setRole(data.data.role); 
      setFiliale(data.data.filiale.id);   
    })
    .catch((error) => {
      console.log(error);
    })
  }

  if (id) {
    useEffect(() => {
      getUser();
      getRoles();
      getFiliales();
    }, [])
  }

  const onSubmit = (ev) => {
    ev.preventDefault();
    if (user.id) {
      axiosClient.put(`/users/${user.id}`, user)
      .then(() => {
        //TODO show notification
        setNotification("User was successfuly Update")
        navigate('/users')
      })
      .catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response?.data.errors
          })        
        }
      })
    } else {
    axiosClient.post('/users', user)
      .then(({data}) => {
        setNotification('user role addite')
        setAdditing(true);
        setRole("basic")
        getRoles();
        setUserID(data.id);
        getFiliales()
      })
      .catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response.data.errors
          })
          console.log(response.data.errors);
        }
      })
    }
  }

  const onValid = (data) => {
    axiosClient.put(`/roles/${id ? id : userID}`, {
      role_name: data.role
    }).then(response => {
      console.log(response.data)
      setNotification("Role User was successfuly Update")
      id ? navigate('/users') : setAddFiliale(true)
    }).catch(error => {
      setError('server', {
        message: response.data.errors
      })      
    });
  }
  const onFocus = (data) => {
    if (!addFiliale) 
    {
      axiosClient.put(`/filiale/${id}`, {
        filiale_id: data.filiale
      }).then(response => {
        setNotification("Filiale User was successfuly Update")
        navigate('/users')
      }).catch(error => {
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response.data.errors
          })        
        }
      });    
    } else {
      axiosClient.post(`/filiale/${id ? id : userID}`, {
        filiale_id: data.filiale
      }).then(response => {
        setNotification("Filiale User was successfuly Update")
        navigate('/users')
      }).catch(err => {
        const response = err.response;
        if (response && response.status === 422) {
          setError('server', {
            message: response.data.errors
          })        
        }
      })
    }
  }

  console.log(loading)
  return (
    <>
      {id && <h1> Update user: {user.name} </h1>}
      {!id && <h1> New user </h1>}
      {(!additing && !addFiliale) && <div className="card animated fadeInDown">
      {/* {loading && (
          <div className="text-center">
            Loading...
          </div>
      )} */}
        {errors?.server?.message &&
          <div className="alert">
            {Object.keys(errors?.server?.message).map(key => (
              <p>{errors?.server?.message[key][0]}</p>
            ))}
          </div>
        }
        {(!additing && !addFiliale) &&
          <form onSubmit={onSubmit}>
            <input onChange={ev => setUser({...user, name: ev.target.value})}  value={user.name} type="text" placeholder='Name'/>
            <input onChange={ev => setUser({...user, email: ev.target.value})}  value={user.email} type="email" placeholder='Email' />
            <input onChange={ev => setUser({...user, password: ev.target.value})}  type="password" placeholder='Password' />
            <input onChange={ev => setUser({...user, password_confirmation: ev.target.value})}  type="password" placeholder='Password Confirmation'/>
            <button className='btn' disabled={!(role && !addFiliale)}> Save </button>
          </form>
          }
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
                      {(roles !== null && roles !== undefined) && roles?.map(role => (
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
      {(!(role && !addFiliale) && !loading) && <Box style={{
        width: "100%",
        height: "150px",
        display: "flex",
        justifyContent: "center",
        alignItems: "center",
      }}> <CircularProgress disableShrink /> </Box>}
      
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
                    {(filiales !== null && filiales !== undefined && (Object.keys(filiales).length !== 0)) 
                    && filiales?.map(filiale => (
                      <MenuItem value={filiale.id} key={filiale.id}> {filiale.name} </MenuItem>
                    ))}
                  </Select>
                </FormControl>)}
            />  
            <Box display="flex" justifyContent="end" mt="20px">
                <Button  type="submit" color="success" variant="contained">
                    {!addFiliale ? "Update" : "Add"} Filiale user
                </Button>
            </Box>
          </Box>
        </form>
      </div>
      } 
      {(!(filiale || addFiliale) && !loading) && <Box style={{
          width: "100%",
          height: "150px",
          display: "flex",
          justifyContent: "center",
          alignItems: "center",
        }}> <CircularProgress disableShrink /> </Box>}
    </>
  )
}
